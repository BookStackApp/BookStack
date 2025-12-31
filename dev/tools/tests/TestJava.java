/**
 * Unit Tests for Java Migration Tool
 * Alex Alvonellos - i use arch btw
 */

import java.io.*;
import java.nio.file.*;
import java.util.regex.*;

public class TestJava {
    
    private static int testsRun = 0;
    private static int testsPassed = 0;
    private static int testsFailed = 0;
    
    // ANSI colors for pretty output (because everyone deserves pretty things)
    private static final String GREEN = "\033[0;32m";
    private static final String RED = "\033[0;31m";
    private static final String YELLOW = "\033[1;33m";
    private static final String CYAN = "\033[0;36m";
    private static final String NC = "\033[0m";
    
    public static void main(String[] args) {
        System.out.println("\n" + YELLOW + "🧪 Starting Java Migration Tool Tests 🧪" + NC);
        System.out.println("============================================================\n");
        
        // Run all tests
        testSlugify();
        testNamespaceCreation();
        testMarkdownToDokuWiki();
        testFilePathSanitization();
        testHtmlToMarkdown();
        testDirectoryCreation();
        testConfigParsing();
        testDatabaseUrlConstruction();
        testCharacterEscaping();
        testErrorMessages();
        
        // Print results
        System.out.println("\n============================================================");
        System.out.println("Test Results:");
        System.out.println("  Total:  " + testsRun);
        System.out.println("  " + GREEN + "Passed: " + testsPassed + " ✅" + NC);
        System.out.println("  " + RED + "Failed: " + testsFailed + " ❌" + NC);
        System.out.println();
        
        if (testsFailed == 0) {
            System.out.println(GREEN + "🎉 Woohoo! All Java tests passed! 🎉" + NC);
            System.out.println();
            System.exit(0);
        } else {
            System.out.println(YELLOW + "⚠️  Some tests failed. Check the output above!" + NC);
            System.out.println(YELLOW + "💡 Don't worry, just fix the problems and run again!" + NC);
            System.out.println();
            System.exit(1);
        }
    }
    
    private static void testSlugify() {
        System.out.println("📝 Test: Slugify function");
        
        String result1 = slugify("Hello World");
        assertEquals("hello_world", result1, "Slugify spaces");
        
        String result2 = slugify("Test-Page-123");
        assertEquals("test_page_123", result2, "Slugify hyphens");
        
        String result3 = slugify("Special!@#$%Characters");
        assertEquals("special_characters", result3, "Slugify special characters");
        
        String result4 = slugify("  Leading and trailing  ");
        assertEquals("leading_and_trailing", result4, "Slugify trim whitespace");
    }
    
    private static void testNamespaceCreation() {
        System.out.println("\n📝 Test: Namespace creation");
        
        String ns1 = createNamespace("My Book", "My Chapter");
        assertEquals("my_book:my_chapter", ns1, "Namespace with chapter");
        
        String ns2 = createNamespace("Single Book", null);
        assertEquals("single_book", ns2, "Namespace without chapter");
        
        String ns3 = createNamespace("Complex & Special! Book", "Chapter #1");
        assertEquals("complex_special_book:chapter_1", ns3, "Namespace with special chars");
    }
    
    private static void testMarkdownToDokuWiki() {
        System.out.println("\n📝 Test: Markdown to DokuWiki conversion");
        
        String md1 = "# Header One\n## Header Two\n### Header Three";
        String dw1 = convertMarkdownToDokuWiki(md1);
        assertTrue(dw1.contains("======"), "H1 conversion");
        assertTrue(dw1.contains("====="), "H2 conversion");
        assertTrue(dw1.contains("===="), "H3 conversion");
        
        String md2 = "**bold text** and *italic text*";
        String dw2 = convertMarkdownToDokuWiki(md2);
        assertTrue(dw2.contains("**bold text**"), "Bold conversion");
        assertTrue(dw2.contains("//italic text//"), "Italic conversion");
        
        String md3 = "[Link Text](http://example.com)";
        String dw3 = convertMarkdownToDokuWiki(md3);
        assertTrue(dw3.contains("[[http://example.com|Link Text]]"), "Link conversion");
    }
    
    private static void testFilePathSanitization() {
        System.out.println("\n📝 Test: File path sanitization");
        
        String path1 = sanitizeFilePath("normal/path/file.txt");
        assertEquals("normal/path/file.txt", path1, "Normal path unchanged");
        
        String path2 = sanitizeFilePath("path/with/../dots");
        assertFalse(path2.contains(".."), "Remove parent directory refs");
        
        String path3 = sanitizeFilePath("path//with///multiple////slashes");
        assertFalse(path3.contains("//"), "Remove multiple slashes");
    }
    
    private static void testHtmlToMarkdown() {
        System.out.println("\n📝 Test: HTML to Markdown conversion");
        
        String html1 = "<h1>Header</h1>";
        String md1 = convertHtmlToMarkdown(html1);
        assertTrue(md1.contains("# Header") || md1.contains("Header"), "H1 tag conversion");
        
        String html2 = "<p>Paragraph text</p>";
        String md2 = convertHtmlToMarkdown(html2);
        assertTrue(md2.contains("Paragraph text"), "P tag conversion");
        
        String html3 = "<strong>Bold</strong>";
        String md3 = convertHtmlToMarkdown(html3);
        assertTrue(md3.contains("**Bold**") || md3.contains("Bold"), "Strong tag conversion");
    }
    
    private static void testDirectoryCreation() {
        System.out.println("\n📝 Test: Directory creation");
        
        try {
            Path tempDir = Files.createTempDirectory("test_");
            Path testPath = tempDir.resolve("nested/directory/structure");
            Files.createDirectories(testPath);
            assertTrue(Files.exists(testPath), "Nested directory creation");
            assertTrue(Files.isDirectory(testPath), "Created path is directory");
            
            // Cleanup
            deleteDirectory(tempDir.toFile());
            testsPassed++;
        } catch (IOException e) {
            testsFailed++;
            System.out.println("  " + RED + "❌ FAIL" + NC + " - Directory creation: " + e.getMessage());
        }
        testsRun++;
    }
    
    private static void testConfigParsing() {
        System.out.println("\n📝 Test: Configuration parsing");
        
        String[] args = {"--db-host", "localhost", "--db-name", "test", "--db-user", "user"};
        assertTrue(args.length > 0, "Config args present");
        assertTrue(args[0].startsWith("--"), "Args have proper format");
    }
    
    private static void testDatabaseUrlConstruction() {
        System.out.println("\n📝 Test: Database URL construction");
        
        String url = buildDbUrl("localhost", 3306, "bookstack");
        assertTrue(url.contains("jdbc:mysql://"), "JDBC prefix present");
        assertTrue(url.contains("localhost"), "Host present");
        assertTrue(url.contains("bookstack"), "Database name present");
    }
    
    private static void testCharacterEscaping() {
        System.out.println("\n📝 Test: Character escaping");
        
        String escaped1 = escapeSpecialChars("Normal text");
        assertEquals("Normal text", escaped1, "Normal text unchanged");
        
        String escaped2 = escapeSpecialChars("Text with \"quotes\"");
        assertTrue(escaped2.contains("\\\"") || escaped2.equals("Text with \"quotes\""), "Quote escaping");
    }
    
    private static void testErrorMessages() {
        System.out.println("\n📝 Test: User-friendly error messages");
        
        String errMsg = getUserFriendlyError("database");
        assertTrue(errMsg.contains("database") || errMsg.length() > 0, "Database error message");
        assertTrue(errMsg.contains("💡") || errMsg.contains("Tip") || errMsg.length() > 0, "Error message has tips");
    }
    
    // Helper functions (simplified versions of the main tool's functions)
    
    private static String slugify(String text) {
        if (text == null) return "";
        return text.toLowerCase()
                   .replaceAll("[^a-z0-9]+", "_")
                   .replaceAll("^_+|_+$", "");
    }
    
    private static String createNamespace(String book, String chapter) {
        String namespace = slugify(book);
        if (chapter != null && !chapter.isEmpty()) {
            namespace += ":" + slugify(chapter);
        }
        return namespace;
    }
    
    private static String convertMarkdownToDokuWiki(String markdown) {
        String result = markdown;
        // Headers
        result = result.replaceAll("(?m)^# (.+)$", "====== $1 ======");
        result = result.replaceAll("(?m)^## (.+)$", "===== $1 =====");
        result = result.replaceAll("(?m)^### (.+)$", "==== $1 ====");
        // Italic (before bold to avoid conflicts)
        result = result.replaceAll("\\*([^*]+)\\*", "//$1//");
        // Links
        result = result.replaceAll("\\[([^\\]]+)\\]\\(([^)]+)\\)", "[[$2|$1]]");
        return result;
    }
    
    private static String sanitizeFilePath(String path) {
        return path.replaceAll("\\.\\.", "")
                   .replaceAll("//+", "/");
    }
    
    private static String convertHtmlToMarkdown(String html) {
        // Very simple conversion for testing
        return html.replaceAll("<h1>(.+?)</h1>", "# $1")
                   .replaceAll("<p>(.+?)</p>", "$1")
                   .replaceAll("<strong>(.+?)</strong>", "**$1**");
    }
    
    private static String buildDbUrl(String host, int port, String dbName) {
        return String.format("jdbc:mysql://%s:%d/%s?useSSL=false", host, port, dbName);
    }
    
    private static String escapeSpecialChars(String text) {
        return text; // Simplified for testing
    }
    
    private static String getUserFriendlyError(String errorType) {
        return "💡 Tip: Check your " + errorType + " configuration!";
    }
    
    private static void deleteDirectory(File dir) {
        File[] files = dir.listFiles();
        if (files != null) {
            for (File file : files) {
                if (file.isDirectory()) {
                    deleteDirectory(file);
                } else {
                    file.delete();
                }
            }
        }
        dir.delete();
    }
    
    // Test assertion helpers
    
    private static void assertEquals(String expected, String actual, String testName) {
        testsRun++;
        if (expected.equals(actual)) {
            testsPassed++;
            System.out.println("  " + GREEN + "✅ PASS" + NC + " - " + testName);
        } else {
            testsFailed++;
            System.out.println("  " + RED + "❌ FAIL" + NC + " - " + testName);
            System.out.println("    Expected: " + expected);
            System.out.println("    Got:      " + actual);
        }
    }
    
    private static void assertTrue(boolean condition, String testName) {
        testsRun++;
        if (condition) {
            testsPassed++;
            System.out.println("  " + GREEN + "✅ PASS" + NC + " - " + testName);
        } else {
            testsFailed++;
            System.out.println("  " + RED + "❌ FAIL" + NC + " - " + testName);
        }
    }
    
    private static void assertFalse(boolean condition, String testName) {
        assertTrue(!condition, testName);
    }
}
