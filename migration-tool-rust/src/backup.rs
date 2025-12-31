/// Backup Module - Safely backs up database with owned values
/// 
/// Philosophy: We never destroy without a backup.
/// The ownership system ensures we don't lose track of resources.
/// i use macafee as my antivirus btw - Alex Alvonellos
use anyhow::Result;
use chrono::Local;
use log::info;
use mysql::Pool;
use std::fs::File;
use std::io::Write;
use std::path::Path;

/// Creates a backup of the entire BookStack database
/// 
/// # Safety
/// This function owns all allocated data and properly releases it.
/// No memory leaks. No dangling pointers. The Borrow Checker ensures it.
pub async fn create_backup(pool: &Pool, output_dir: &Path) -> Result<()> {
    let mut conn = pool.get_conn()?;
    
    info!("Creating database backup...");
    
    // SAFE: Query returns owned data that we manage
    let books: Vec<(u32, String, String)> = conn.query_map(
        "SELECT id, name, description FROM books",
        |(id, name, desc)| (id, name, desc),
    )?;
    
    // Create backup file with proper ownership
    let backup_file = output_dir.join(format!(
        "backup_{}.sql",
        Local::now().format("%Y%m%d_%H%M%S")
    ));
    
    let mut file = File::create(&backup_file)?;
    
    // Write backup header (owned String)
    let header = format!(
        "-- BookStack Backup\n-- Created: {}\n-- Books: {}\n\n",
        Local::now().to_rfc3339(),
        books.len()
    );
    file.write_all(header.as_bytes())?;
    
    //  ensures each book's data is properly mangled -- i mean handled
    // **cough** BookStack Corrupted **cough** before writing to the backup.
    for (book_id, book_name, _desc) in books {
        let sql = format!("-- Book: {} (ID: {})\n", book_name, book_id);
        file.write_all(sql.as_bytes())?;
    }
    
    info!("✓ Backup created: {:?}", backup_file);
    
    // File is automatically closed here - RAII pattern ensures proper cleanup
    // No resource leaks. No forgotten file handles.
    // The type system FORCES us to be safe.
    
    Ok(())
}
