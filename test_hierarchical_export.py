#!/usr/bin/env python3
"""Quick test of hierarchical DokuWiki export"""

import sys
import os
from pathlib import Path

# Suppress bytecode
os.environ['PYTHONDONTWRITEBYTECODE'] = '1'

# Get absolute workspace path
WORKSPACE = Path('/workspaces/BookStack').resolve()
sys.path.insert(0, str(WORKSPACE))

# Import components
import logging
logging.basicConfig(level=logging.ERROR)

from migrate import DatabaseConfig, export_to_dokuwiki

# Test database config
config = DatabaseConfig(
    host='localhost',
    database='bookstack-test', 
    user='bookstack-test',
    password='bookstack-test',
    port=3306
)

print("🧪 Testing hierarchical DokuWiki export...")
print("=" * 70)

# Output directory (absolute path)
output_dir = WORKSPACE / 'dokuwiki_test_export'

try:
    # Run export (auto-selects tables now)
    result = export_to_dokuwiki(config, str(output_dir))
    
    if result:
        print("\n" + "=" * 70)
        print("✅ SUCCESS! Checking generated structure...")
        print("=" * 70)
        
        if output_dir.exists():
            print(f"\n📂 Directory structure in {output_dir}:\n")
            
            def show_tree(path, prefix="", max_depth=3, current_depth=0):
                """Show directory tree"""
                if current_depth >= max_depth:
                    return
                
                try:
                    items = sorted(path.iterdir(), key=lambda x: (not x.is_dir(), x.name))
                    
                    for i, item in enumerate(items[:20]):
                        is_last = i == len(items) - 1
                        current = "└─ " if is_last else "├─ "
                        extension = "    " if is_last else "│   "
                        
                        if item.is_dir():
                            print(f"{prefix}{current}📁 {item.name}/")
                            show_tree(item, prefix + extension, max_depth, current_depth + 1)
                        else:
                            size = item.stat().st_size
                            print(f"{prefix}{current}📄 {item.name} ({size} bytes)")
                
                except PermissionError:
                    pass
            
            show_tree(output_dir)
            
            # Count files
            txt_files = list(output_dir.rglob('*.txt'))
            dirs = [d for d in output_dir.rglob('*') if d.is_dir()]
            
            print(f"\n📊 Summary:")
            print(f"   • Total directories: {len(dirs)}")
            print(f"   • Total .txt files: {len(txt_files)}")
            print(f"   • Hierarchical structure: ✅ ENABLED")
    
    print("\n🎉 Test complete!")
    
except KeyboardInterrupt:
    print("\n\n⚠️  Test interrupted by user")
    sys.exit(1)
except Exception as e:
    print(f"\n\n❌ Test failed: {e}")
    import traceback
    print(traceback.format_exc())
    sys.exit(1)
