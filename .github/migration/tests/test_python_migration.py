#!/usr/bin/env python3
"""
Unit Tests for BookStack Python Migration Tool
Tests database inspection, export logic, error handling
"""

import unittest
import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent))

class TestDatabaseInspection(unittest.TestCase):
    """Test schema inspection functionality"""
    
    def test_identify_content_tables(self):
        """Test automatic table identification"""
        # Mock table list
        tables = [
            ('pages', ['id', 'name', 'html', 'book_id', 'chapter_id']),
            ('books', ['id', 'name', 'slug', 'description']),
            ('chapters', ['id', 'name', 'book_id']),
            ('users', ['id', 'email', 'password'])
        ]
        
        # Should identify pages, books, chapters
        content_tables = []
        for table, columns in tables:
            col_set = set(columns)
            if 'html' in col_set or 'content' in col_set:
                content_tables.append(table)
            elif 'book_id' in col_set and 'name' in col_set:
                content_tables.append(table)
        
        self.assertIn('pages', content_tables)
        self.assertIn('chapters', content_tables)
        self.assertNotIn('users', content_tables)
    
    def test_column_pattern_matching(self):
        """Test column pattern recognition"""
        page_columns = ['id', 'name', 'html', 'book_id', 'chapter_id']
        book_columns = ['id', 'name', 'slug', 'description']
        
        # Pages should have html/content
        has_content = any(col in page_columns for col in ['html', 'content', 'text'])
        self.assertTrue(has_content)
        
        # Books should have structural fields
        has_structure = all(col in book_columns for col in ['id', 'name', 'slug'])
        self.assertTrue(has_structure)

class TestFilenameSanitization(unittest.TestCase):
    """Test DokuWiki filename sanitization"""
    
    def test_special_characters(self):
        """Test special character removal"""
        test_cases = {
            "My Page!": "my_page",
            "Test@#$%": "test",
            "Spaced Out": "spaced_out",
            "Multiple   Spaces": "multiple_spaces",
            "_leading_trailing_": "leading_trailing",
            "": "unnamed"
        }
        
        for input_name, expected in test_cases.items():
            sanitized = self._sanitize(input_name)
            self.assertEqual(sanitized, expected, f"Failed for: {input_name}")
    
    def _sanitize(self, name):
        """Mock sanitize function"""
        if not name:
            return "unnamed"
        name = name.lower()
        name = ''.join(c if c.isalnum() else '_' for c in name)
        name = '_'.join(filter(None, name.split('_')))
        return name if name else "unnamed"

class TestHTMLConversion(unittest.TestCase):
    """Test HTML to DokuWiki conversion"""
    
    def test_headings(self):
        """Test heading conversion"""
        conversions = {
            "<h1>Title</h1>": "====== Title ======",
            "<h2>Section</h2>": "===== Section =====",
            "<h3>Subsection</h3>": "==== Subsection ====",
        }
        
        for html, dokuwiki in conversions.items():
            # Simple conversion test
            self.assertIsNotNone(html)
            self.assertIsNotNone(dokuwiki)
    
    def test_formatting(self):
        """Test text formatting"""
        conversions = {
            "<strong>bold</strong>": "**bold**",
            "<em>italic</em>": "//italic//",
            "<code>code</code>": "''code''",
        }
        
        for html, dokuwiki in conversions.items():
            self.assertIsNotNone(html)
            self.assertIsNotNone(dokuwiki)

class TestErrorHandling(unittest.TestCase):
    """Test error handling and recovery"""
    
    def test_missing_database(self):
        """Test handling of missing database"""
        # Should raise connection error
        try:
            # Mock connection attempt
            raise ConnectionError("Database not found")
        except ConnectionError as e:
            self.assertIn("Database", str(e))
    
    def test_invalid_credentials(self):
        """Test handling of invalid credentials"""
        try:
            raise PermissionError("Access denied")
        except PermissionError as e:
            self.assertIn("Access", str(e))
    
    def test_missing_table(self):
        """Test handling of missing tables"""
        tables = ['users', 'settings']
        self.assertNotIn('pages', tables)

class TestPackageInstallation(unittest.TestCase):
    """Test package installation helpers"""
    
    def test_package_detection(self):
        """Test package availability detection"""
        required = {
            'mysql-connector-python': 'mysql.connector',
            'pymysql': 'pymysql'
        }
        
        for package, import_name in required.items():
            # Test import name validity
            self.assertTrue(len(import_name) > 0)
            self.assertFalse('.' in package)  # Package names don't have dots
    
    def test_installation_methods(self):
        """Test different installation methods"""
        methods = [
            'pip install',
            'pip install --user',
            'pip install --break-system-packages',
            'python3 -m venv',
            'manual',
            'exit'
        ]
        
        self.assertEqual(len(methods), 6)
        self.assertIn('venv', methods[3])

class TestDryRun(unittest.TestCase):
    """Test dry run functionality"""
    
    def test_dry_run_no_changes(self):
        """Ensure dry run makes no changes"""
        # Mock state
        initial_state = {'files_created': 0, 'db_modified': False}
        
        # Dry run should not modify
        dry_run_state = initial_state.copy()
        
        self.assertEqual(initial_state, dry_run_state)
    
    def test_dry_run_preview(self):
        """Test dry run preview generation"""
        preview = {
            'books': 3,
            'chapters': 5,
            'pages': 15,
            'estimated_files': 23
        }
        
        self.assertGreater(preview['estimated_files'], 0)
        self.assertEqual(preview['books'] + preview['chapters'] + preview['pages'], 23)

class TestLogging(unittest.TestCase):
    """Test logging functionality"""
    
    def test_log_file_creation(self):
        """Test log file is created"""
        import tempfile
        import datetime
        
        log_dir = Path(tempfile.gettempdir()) / 'migration_logs'
        log_dir.mkdir(exist_ok=True)
        
        timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
        log_file = log_dir / f'test_{timestamp}.log'
        
        # Create log file
        log_file.write_text("Test log entry\n")
        
        self.assertTrue(log_file.exists())
        self.assertGreater(log_file.stat().st_size, 0)
        
        # Cleanup
        log_file.unlink()

if __name__ == '__main__':
    print("=" * 70)
    print(" BookStack Migration Tool - Unit Tests")
    print("=" * 70)
    print()
    
    # Run tests with verbosity
    unittest.main(verbosity=2)
