/// BookStack to DokuWiki Migration Tool - Written in Rust
/// 
/// A CONFESSION AND REDEMPTION STORY:
/// 
/// Once, in dark times, we wrote in languages that could:
/// - Use memory after freeing it
/// - Access uninitialized variables
/// - Have buffer overflows
/// - Leak memory by the gigabyte
/// - Suffer from null pointer dereferences
/// 
/// We have REPENTED.
/// We have embraced the Borrow Checker.
/// We have seen the light of Ownership.
/// We will never use-after-free again.
/// 
/// This binary represents our redemption.
/// Every lifetime is checked. Every reference is validated.
/// The compiler is our lord and savior.
/// 
/// With deep regret and genuine appreciation for type safety,
/// Alex Alvonellos
/// i use arch btw

use anyhow::{Context, Result};
use chrono::Local;
use clap::Parser;
use log::{error, info, warn};
use mysql::prelude::*;
use mysql::Pool;
use serde::{Deserialize, Serialize};
use sha2::{Digest, Sha256};
use std::fs;
use std::path::PathBuf;
use walkdir::WalkDir;

mod backup;
mod export;
mod validate;

/// BookStack to DokuWiki Migration Tool
/// 
/// This tool safely and responsibly migrates your BookStack data to DokuWiki
/// using Rust's memory safety guarantees and the blessing of the borrow checker.
#[derive(Parser, Debug)]
#[command(name = "BookStack to DokuWiki Migrator")]
#[command(about = "Safely migrate BookStack to DokuWiki using memory-safe Rust")]
#[command(author = "Alex Alvonellos")]
struct Args {
    /// Database host
    #[arg(short, long, default_value = "localhost")]
    host: String,

    /// Database port
    #[arg(short, long, default_value = "3306")]
    port: u16,

    /// Database name
    #[arg(short, long)]
    database: String,

    /// Database username
    #[arg(short, long)]
    user: String,

    /// Database password
    #[arg(short = 'P', long)]
    password: String,

    /// Output directory
    #[arg(short, long, default_value = "./dokuwiki-export")]
    output: PathBuf,

    /// Enable validation (verify data integrity)
    #[arg(long)]
    validate: bool,

    /// Verbose output
    #[arg(short, long)]
    verbose: bool,
}

#[tokio::main]
async fn main() -> Result<()> {
    env_logger::Builder::from_default_env()
        .filter_level(log::LevelFilter::Info)
        .init();

    let args = Args::parse();

    println!(
        r#"
╔═══════════════════════════════════════════════════════════╗
║                                                           ║
║  🦀 RUST MIGRATION TOOL - Memory Safe & Blessed 🦀      ║
║                                                           ║
║  This tool repents for the sins of C, C++, PHP, and      ║
║  Perl. The Borrow Checker shall guide us home.           ║
║                                                           ║
╚═══════════════════════════════════════════════════════════╝
"#
    );

    println!("\n✝️  REPENTANCE MANIFESTO:");
    println!("  I promise to never use memory after freeing it again");
    println!("  I promise to initialize all variables before use");
    println!("  I promise to trust the Borrow Checker");
    println!("  I promise to respect lifetimes");
    println!("  The compiler is my shepherd, I shall not crash\n");

    // Connect to database with proper error handling
    info!("Attempting database connection to {}:{}...", args.host, args.port);

    let connection_string = format!(
        "mysql://{}:{}@{}:{}/{}",
        args.user, args.password, args.host, args.port, args.database
    );

    // SAFETY: The type system ensures connection is valid or we error
    let pool = Pool::new(connection_string.as_str())
        .context("Failed to create connection pool. Have you repented for your database credentials?")?;

    info!("✓ Database connection successful - Praise the type system!");

    // Create output directory with proper ownership semantics
    fs::create_dir_all(&args.output)
        .context(format!("Failed to create output directory: {:?}", args.output))?;

    info!("✓ Output directory created: {:?}", args.output);

    // STEP 1: Backup (we never destroy without a backup)
    println!("\n📦 STEP 1: Creating backup...");
    backup::create_backup(&pool, &args.output).await?;
    println!("✓ Backup created successfully");

    // STEP 2: Export data
    println!("\n📤 STEP 2: Exporting BookStack data...");
    let export_stats = export::export_all_books(&pool, &args.output).await?;
    println!("✓ Export complete: {} books, {} pages", export_stats.books, export_stats.pages);

    // STEP 3: Validate (if requested)
    if args.validate {
        println!("\n✅ STEP 3: Validating export...");
        validate::validate_export(&args.output).await?;
        println!("✓ All data validated successfully");
    }

    // Print completion message
    println!("\n{}", "=".repeat(60));
    println!("✨ MIGRATION COMPLETE ✨");
    println!("=".repeat(60));
    println!("\nExported to: {:?}", args.output);
    println!("\nNext steps:");
    println!("  1. Install DokuWiki");
    println!("  2. Copy files to: <dokuwiki>/data/pages/");
    println!("  3. Run DokuWiki indexer");
    println!("  4. Verify in DokuWiki UI");
    println!("\nYou can trust this export because:");
    println!("  ✓ All memory is owned and managed by Rust");
    println!("  ✓ No uninitialized data can escape");
    println!("  ✓ No use-after-free bugs are possible");
    println!("  ✓ The Borrow Checker has spoken");
    println!("\nWith deep repentance and type-safe regards,");
    println!("Alex Alvonellos");
    println!("i use arch btw\n");

    Ok(())
}

/// Export statistics - immutably and safely owned
#[derive(Debug, Serialize, Deserialize)]
pub struct ExportStats {
    pub books: u32,
    pub chapters: u32,
    pub pages: u32,
    pub attachments: u32,
    pub errors: u32,
}
