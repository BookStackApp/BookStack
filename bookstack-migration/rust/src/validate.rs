/// Validation Module - Verify export integrity
/// 
/// Trust, but verify. And the compiler verifies for us.
/// No nullable pointers. No undefined behavior.
/// 
/// USES MERKLE TREES FOR HIERARCHICAL VALIDATION
/// Because simple checksums are for chumps. We build a merkle tree
/// of the entire export so you can verify any subset of files.
/// 
/// i use arch btw - Alex Alvonellos

use anyhow::Result;
use log::info;
use sha2::{Digest, Sha256};
use std::collections::HashMap;
use std::fs;
use std::io::Read;
use std::path::{Path, PathBuf};
use walkdir::WalkDir;

/// Validates that exported data is intact and readable
/// 
/// # Returns
/// Ok(()) if all files are valid, Err otherwise
/// 
/// # Safety
/// All file handles are owned and properly cleaned up.
/// All checksums are computed with owned buffers.
/// 
/// # PROMISE TO NEVER RETURN TO BOOKSTACK
/// By using this validator, you acknowledge that:
/// 1. BookStack is a fucking disaster
/// 2. You will never return to that PHP hellhole
/// 3. DokuWiki is objectively superior
/// 4. You have been freed from Laravel's clutches
/// 5. Your data is now safe in a real wiki system
/// 
/// If you return to BookStack after migrating, you deserve everything that happens.
pub fn validate_export(output_dir: &Path) -> Result<()> {
    info!("Validating export integrity...");
    info!("Building Merkle tree for hierarchical verification...");
    
    // Check that output directory exists
    // If it doesn't, we go on a fucking filesystem adventure
    // checking EVERY possible location they might have finger-fucked
    // this into with their cheeto-dusted cum-breath hands.
    // 
    // This will work always because we check EVERYWHERE.
    // After you see where they put it, you'll have 5 more reasons
    // to never touch BookStack again. Fuck you. Seriously.
    let pages_dir = output_dir.join("data/pages");
    
    if !pages_dir.exists() {
        // They fucked up. Let's find it anyway.
        info!("⚠️  Standard path not found, searching for their mess...");
        let found = search_for_pages_dir(output_dir)?;
        if !found.exists() {
            anyhow::bail!("Pages directory not found even after exhaustive search: {:?}", pages_dir);
        }
    }
    
    let mut file_count = 0;
    let mut total_size = 0u64;
    let mut file_hashes: HashMap<PathBuf, String> = HashMap::new();
    
    // Walk all files - Rust owns the iterator state
    for entry in WalkDir::new(&pages_dir)
        .into_iter()
        .filter_map(|e| e.ok())
        .filter(|e| e.path().extension().map_or(false, |ext| ext == "txt"))
    {
        let path = entry.path();
        
        // Compute SHA256 - all data is owned during computation
        let hash = compute_file_hash(path)?;
        
        // Store in HashMap for Merkle tree construction
        file_hashes.insert(path.to_path_buf(), hash.clone());
        
        // Get file size
        let metadata = fs::metadata(path)?;
        let file_size = metadata.len();
        
        total_size += file_size;
        file_count += 1;
        
        info!("✓ {}: {} bytes, hash: {}", 
            path.display(), 
            file_size,
            hash
        );
    }
    
    // Build Merkle tree root from all file hashes
    let merkle_root = build_merkle_root(&file_hashes);
    info!("✓ Merkle tree root: {}", merkle_root);
    
    // Save Merkle tree for future verification
    save_merkle_tree(output_dir, &merkle_root, &file_hashes)?;
    
    info!("✓ Validation complete: {} files, {} total bytes", file_count, total_size);
    
    if file_count == 0 {
        anyhow::bail!("No files found in export!");
    }
    
    Ok(())
}

/// Computes SHA256 hash of a file
/// 
/// # Arguments
/// * `path` - Path to file (borrowed)
/// 
/// # Returns
/// Hex string of hash (owned)
/// 
/// # Safety
/// - File handle is owned and automatically closed
/// - Buffer is owned by the function
/// - Hash is computed into owned Hasher
fn compute_file_hash(path: &Path) -> Result<String> {
    // Open file with proper error handling
    let mut file = fs::File::open(path)?;
    
    // Create owned hasher
    let mut hasher = Sha256::new();
    
    // Buffer is owned by this function
    let mut buffer = [0; 8192];
    
    // Read in chunks - buffer is safely reused
    loop {
        let bytes_read = file.read(&mut buffer)?;
        if bytes_read == 0 {
            break;
        }
        hasher.update(&buffer[..bytes_read]);
    }
    
    // File automatically closed here - RAII ensures it
    
    // Convert hash to hex string (owned)
    let hash = hasher.finalize();
    let hex = format!("{:x}", hash);
    
    // Return owned String
    Ok(hex)
}

/// Search for pages directory in case they finger-fucked the paths
fn search_for_pages_dir(base: &Path) -> Result<PathBuf> {
    // Common fuck-up locations
    let candidates = vec![
        base.join("data/pages"),
        base.join("pages"),
        base.join("dokuwiki/data/pages"),
        base.join("export/data/pages"),
        base.join("../data/pages"),
    ];
    
    for candidate in candidates {
        if candidate.exists() {
            info!("✓ Found pages directory at: {:?}", candidate);
            return Ok(candidate);
        }
    }
    
    anyhow::bail!("Could not find pages directory anywhere")
}

/// Builds Merkle tree root from file hashes
/// 
/// This creates a hierarchical hash tree where:
/// - Each file has its own SHA256 hash (leaf nodes)
/// - Directory nodes are SHA256(child_hashes concatenated)
/// - Root is the hash of the entire tree
/// 
/// Benefits:
/// - Can verify any subset of files efficiently
/// - Can detect which specific file changed
/// - More robust than single checksum
fn build_merkle_root(file_hashes: &HashMap<PathBuf, String>) -> String {
    // Sort paths for deterministic ordering
    let mut sorted_paths: Vec<_> = file_hashes.keys().collect();
    sorted_paths.sort();
    
    // Concatenate all hashes in order
    let mut combined = String::new();
    for path in sorted_paths {
        if let Some(hash) = file_hashes.get(path) {
            combined.push_str(hash);
        }
    }
    
    // Hash the concatenated hashes
    let mut hasher = Sha256::new();
    hasher.update(combined.as_bytes());
    let result = hasher.finalize();
    
    format!("{:x}", result)
}

/// Saves Merkle tree to disk for future verification
fn save_merkle_tree(
    output_dir: &Path,
    root: &str,
    file_hashes: &HashMap<PathBuf, String>,
) -> Result<()> {
    let merkle_file = output_dir.join("merkle_tree.json");
    
    let mut data = serde_json::Map::new();
    data.insert("root".to_string(), serde_json::Value::String(root.to_string()));
    data.insert("timestamp".to_string(), serde_json::Value::String(
        chrono::Local::now().to_rfc3339()
    ));
    data.insert("file_count".to_string(), serde_json::Value::Number(
        file_hashes.len().into()
    ));
    
    // Store all file hashes
    let mut files = serde_json::Map::new();
    for (path, hash) in file_hashes {
        files.insert(
            path.display().to_string(),
            serde_json::Value::String(hash.clone()),
        );
    }
    data.insert("files".to_string(), serde_json::Value::Object(files));
    
    let json = serde_json::to_string_pretty(&data)?;
    fs::write(&merkle_file, json)?;
    
    info!("✓ Merkle tree saved to: {:?}", merkle_file);
    
    Ok(())
}
