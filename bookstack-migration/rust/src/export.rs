/// Export Module - Safely exports BookStack data
/// 
/// Every string is owned. Every Vec is owned. Nothing escapes unmanaged.
/// The Borrow Checker watches over us with infinite mercy.
/// i use arch btw - Alex Alvonellos

use crate::ExportStats;
use anyhow::Result;
use log::info;
use mysql::{prelude::Queryable, Pool};
use std::fs;
use std::path::Path;

/// Exports all books, chapters, and pages from BookStack
/// 
/// # Memory Safety Guarantees
/// - All returned data is owned by the caller
/// - No dangling pointers
/// - No use-after-free bugs
/// - The compiler VERIFIED this at compile time
pub fn export_all_books(pool: &Pool, output_dir: &Path) -> Result<ExportStats> {
    let mut conn = pool.get_conn()?;
    
    info!("Exporting all books from BookStack...");
    
    // SAFE: Query returns owned Vecs that we fully control
    let books: Vec<BookData> = conn.query_map(
        "SELECT id, name, slug FROM books WHERE deleted_at IS NULL ORDER BY id",
        |(id, name, slug)| BookData { id, name, slug },
    )?;
    
    let mut stats = ExportStats {
        books: 0,
        chapters: 0,
        pages: 0,
        attachments: 0,
        errors: 0,
    };
    
    // Create DokuWiki structure
    let pages_dir = output_dir.join("data/pages");
    fs::create_dir_all(&pages_dir)?;
    
    // Process each book - Rust ensures we clean up properly
    for book in books {
        stats.books += 1;
        
        // Create book namespace
        let book_dir = pages_dir.join(&book.slug);
        fs::create_dir_all(&book_dir)?;
        
        // Fetch chapters for this book
        let chapters: Vec<ChapterData> = conn.query_map(
            format!("SELECT id, name, slug FROM chapters WHERE book_id = {} AND deleted_at IS NULL", book.id),
            |(id, name, slug)| ChapterData { id, name, slug },
        )?;
        
        for chapter in chapters {
            stats.chapters += 1;
            
            // Create chapter namespace
            let chapter_dir = book_dir.join(&chapter.slug);
            fs::create_dir_all(&chapter_dir)?;
            
            // Fetch pages for this chapter
            let pages: Vec<PageData> = conn.query_map(
                format!(
                    "SELECT id, name, slug, html FROM pages WHERE chapter_id = {} AND deleted_at IS NULL",
                    chapter.id
                ),
                |(id, name, slug, html)| PageData { id, name, slug, html },
            )?;
            
            for page in pages {
                stats.pages += 1;
                
                // Convert HTML to DokuWiki format
                let dokuwiki_content = convert_html_to_dokuwiki(&page.html);
                
                // Write page file - Rust owns this data
                let page_file = chapter_dir.join(format!("{}.txt", page.slug));
                fs::write(&page_file, dokuwiki_content)?;
                
                info!("✓ Exported: {}/{}/{}", book.slug, chapter.slug, page.slug);
            }
        }
    }
    
    info!("✓ Export complete: {} books, {} pages", stats.books, stats.pages);
    
    Ok(stats)
}

/// Book data - Owned String values ensure no use-after-free
#[derive(Debug, Clone)]
#[allow(dead_code)]
struct BookData {
    id: u32,
    name: String,
    slug: String,
}

/// Chapter data - Everything properly owned
#[derive(Debug, Clone)]
#[allow(dead_code)]
struct ChapterData {
    id: u32,
    name: String,
    slug: String,
}

/// Page data - Full ownership prevents memory errors
#[derive(Debug, Clone)]
#[allow(dead_code)]
struct PageData {
    id: u32,
    name: String,
    slug: String,
    html: String,
}

/// Converts HTML to DokuWiki format
/// 
/// This function receives owned data and returns owned data.
/// No borrowing issues. No lifetime problems.
/// Compile-time verified memory safety.
fn convert_html_to_dokuwiki(html: &str) -> String {
    // Simple conversion rules
    let converted = html
        .replace("<h1>", "====== ")
        .replace("</h1>", " ======")
        .replace("<h2>", "===== ")
        .replace("</h2>", " =====")
        .replace("<h3>", "==== ")
        .replace("</h3>", " ====")
        .replace("<p>", "")
        .replace("</p>", "\n\n")
        .replace("<strong>", "**")
        .replace("</strong>", "**")
        .replace("<em>", "//")
        .replace("</em>", "//")
        .replace("<ul>", "")
        .replace("</ul>", "")
        .replace("<li>", "  * ")
        .replace("</li>", "\n");
    
    // Return owned String - fully managed by caller
    converted
}
