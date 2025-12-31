package com.bookstack.export;

import org.apache.commons.cli.*;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.*;
import java.nio.file.*;
import java.sql.*;
import java.text.SimpleDateFormat;
import java.util.*;
import java.util.Date;

/**
 * BookStack to DokuWiki Exporter
 * 
 * This is the version you use when PHP inevitably has difficulties with your export.
 * It connects directly to the database and doesn't depend on Laravel's
 * "elegant" architecture having a good day.
 * 
 * WARNING: DO NOT MODIFY THIS UNLESS YOU KNOW WHAT YOU'RE DOING.
 * This code exists because frameworks are unreliable. Keep it simple.
 * If you need to add features, create a new class. Don't touch this one.
 * 
 * @author Someone who's tired of the complexity
 * @version 1.3.3.7
 */
public class DokuWikiExporter {
    
    private Connection conn;
    private String outputPath;
    private boolean preserveTimestamps;
    private boolean verbose;
    private int booksExported = 0;
    private int chaptersExported = 0;
    private int pagesExported = 0;
    private int errorsEncountered = 0;

    public static void main(String[] args) {
        /*
         * Main entry point.
         * Parses arguments and runs the export.
         * This is intentionally simple because complexity breeds bugs.
         */
        Options options = new Options();
        
        options.addOption("h", "host", true, "Database host (default: localhost)");
        options.addOption("P", "port", true, "Database port (default: 3306)");
        options.addOption("d", "database", true, "Database name (required)");
        options.addOption("u", "user", true, "Database user (required)");
        options.addOption("p", "password", true, "Database password");
        options.addOption("o", "output", true, "Output directory (default: ./dokuwiki_export)");
        options.addOption("b", "book", true, "Export specific book ID only");
        options.addOption("t", "timestamps", false, "Preserve original timestamps");
        options.addOption("v", "verbose", false, "Verbose output");
        options.addOption("help", false, "Show this help message");
        
        CommandLineParser parser = new DefaultParser();
        HelpFormatter formatter = new HelpFormatter();
        
        try {
            CommandLine cmd = parser.parse(options, args);
            
            if (cmd.hasOption("help")) {
                formatter.printHelp("dokuwiki-exporter", options);
                System.out.println("\nThis is the Java version. Use this when PHP fails you.");
                System.out.println("It connects directly to the database, no framework required.");
                return;
            }
            
            // Validate required options
            if (!cmd.hasOption("database") || !cmd.hasOption("user")) {
                System.err.println("ERROR: Database name and user are required.");
                formatter.printHelp("dokuwiki-exporter", options);
                System.exit(1);
            }
            
            DokuWikiExporter exporter = new DokuWikiExporter();
            exporter.run(cmd);
            
        } catch (ParseException e) {
            System.err.println("Error parsing arguments: " + e.getMessage());
            formatter.printHelp("dokuwiki-exporter", options);
            System.exit(1);
        } catch (Exception e) {
            System.err.println("Export failed: " + e.getMessage());
            e.printStackTrace();
            System.exit(1);
        }
    }
    
    /**
     * Run the export process
     * 
     * CRITICAL: Don't add complexity here. Each step should be obvious.
     * If something fails, we want to know exactly where and why.
     */
    public void run(CommandLine cmd) throws Exception {
        verbose = cmd.hasOption("verbose");
        preserveTimestamps = cmd.hasOption("timestamps");
        outputPath = cmd.getOptionValue("output", "./dokuwiki_export");
        
        log("BookStack to DokuWiki Exporter (Java Edition)");
        log("================================================");
        log("Use this version when PHP has technical difficulties (which is often).");
        log("");
        
        // Connect to database
        String host = cmd.getOptionValue("host", "localhost");
        String port = cmd.getOptionValue("port", "3306");
        String database = cmd.getOptionValue("database");
        String user = cmd.getOptionValue("user");
        String password = cmd.getOptionValue("password", "");
        
        connectDatabase(host, port, database, user, password);
        
        // Create output directory
        Files.createDirectories(Paths.get(outputPath));
        
        // Export books
        String bookId = cmd.getOptionValue("book");
        if (bookId != null) {
            exportBook(Integer.parseInt(bookId));
        } else {
            exportAllBooks();
        }
        
        // Cleanup
        conn.close();
        
        // Display stats
        displayStats();
    }
    
    /**
     * Connect to the database
     * 
     * This uses JDBC directly because we don't need an ORM's overhead.
     * ORMs are where performance goes to die.
     */
    private void connectDatabase(String host, String port, String database, 
                                 String user, String password) throws Exception {
        log("Connecting to database: " + database + "@" + host + ":" + port);
        
        String url = "jdbc:mysql://" + host + ":" + port + "/" + database 
                   + "?useSSL=false&allowPublicKeyRetrieval=true";
        
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            conn = DriverManager.getConnection(url, user, password);
            log("Database connected successfully. Unlike PHP, we won't randomly disconnect.");
        } catch (ClassNotFoundException e) {
            throw new Exception("MySQL driver not found. Did you build the JAR correctly?", e);
        } catch (SQLException e) {
            throw new Exception("Database connection failed: " + e.getMessage(), e);
        }
    }
    
    /**
     * Export all books from the database
     */
    private void exportAllBooks() throws Exception {
        String sql = "SELECT id, name, slug, description, created_at, updated_at " +
                    "FROM books ORDER BY name";
        
        try (Statement stmt = conn.createStatement();
             ResultSet rs = stmt.executeQuery(sql)) {
            
            while (rs.next()) {
                try {
                    exportBookContent(
                        rs.getInt("id"),
                        rs.getString("name"),
                        rs.getString("slug"),
                        rs.getString("description"),
                        rs.getTimestamp("created_at"),
                        rs.getTimestamp("updated_at")
                    );
                } catch (Exception e) {
                    errorsEncountered++;
                    System.err.println("Error exporting book '" + rs.getString("name") + "': " 
                                     + e.getMessage());
                    if (verbose) {
                        e.printStackTrace();
                    }
                }
            }
        }
    }
    
    /**
     * Export a single book by ID
     */
    private void exportBook(int bookId) throws Exception {
        String sql = "SELECT id, name, slug, description, created_at, updated_at " +
                    "FROM books WHERE id = ?";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, bookId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                if (rs.next()) {
                    exportBookContent(
                        rs.getInt("id"),
                        rs.getString("name"),
                        rs.getString("slug"),
                        rs.getString("description"),
                        rs.getTimestamp("created_at"),
                        rs.getTimestamp("updated_at")
                    );
                } else {
                    throw new Exception("Book with ID " + bookId + " not found.");
                }
            }
        }
    }
    
    /**
     * Export book content and structure
     * 
     * IMPORTANT: Don't mess with the directory structure.
     * DokuWiki has specific expectations. Deviation will break things.
     */
    private void exportBookContent(int bookId, String name, String slug, 
                                   String description, Timestamp createdAt, 
                                   Timestamp updatedAt) throws Exception {
        booksExported++;
        log("Exporting book: " + name);
        
        String bookSlug = sanitizeFilename(slug != null ? slug : name);
        Path bookPath = Paths.get(outputPath, bookSlug);
        Files.createDirectories(bookPath);
        
        // Create book start page
        createBookStartPage(bookId, name, description, bookPath, updatedAt);
        
        // Export chapters
        exportChapters(bookId, bookSlug, bookPath);
        
        // Export direct pages (not in chapters)
        exportDirectPages(bookId, bookPath);
    }
    
    /**
     * Create the book's start page (DokuWiki index)
     */
    private void createBookStartPage(int bookId, String name, String description,
                                     Path bookPath, Timestamp updatedAt) throws Exception {
        StringBuilder content = new StringBuilder();
        content.append("====== ").append(name).append(" ======\n\n");
        
        if (description != null && !description.isEmpty()) {
            content.append(convertHtmlToDokuWiki(description)).append("\n\n");
        }
        
        content.append("===== Contents =====\n\n");
        
        // List chapters
        String chapterSql = "SELECT name, slug FROM chapters WHERE book_id = ? ORDER BY priority";
        try (PreparedStatement stmt = conn.prepareStatement(chapterSql)) {
            stmt.setInt(1, bookId);
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    String chapterSlug = sanitizeFilename(
                        rs.getString("slug") != null ? rs.getString("slug") : rs.getString("name")
                    );
                    content.append("  * [[:")
                           .append(sanitizeFilename(name))
                           .append(":")
                           .append(chapterSlug)
                           .append(":start|")
                           .append(rs.getString("name"))
                           .append("]]\n");
                }
            }
        }
        
        // List direct pages
        String pageSql = "SELECT name, slug FROM pages " +
                        "WHERE book_id = ? AND chapter_id IS NULL ORDER BY priority";
        try (PreparedStatement stmt = conn.prepareStatement(pageSql)) {
            stmt.setInt(1, bookId);
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    String pageSlug = sanitizeFilename(
                        rs.getString("slug") != null ? rs.getString("slug") : rs.getString("name")
                    );
                    content.append("  * [[:")
                           .append(sanitizeFilename(name))
                           .append(":")
                           .append(pageSlug)
                           .append("|")
                           .append(rs.getString("name"))
                           .append("]]\n");
                }
            }
        }
        
        Path startFile = bookPath.resolve("start.txt");
        Files.write(startFile, content.toString().getBytes("UTF-8"));
        
        if (preserveTimestamps && updatedAt != null) {
            startFile.toFile().setLastModified(updatedAt.getTime());
        }
    }
    
    /**
     * Export all chapters in a book
     */
    private void exportChapters(int bookId, String bookSlug, Path bookPath) throws Exception {
        String sql = "SELECT id, name, slug, description, created_at, updated_at " +
                    "FROM chapters WHERE book_id = ? ORDER BY priority";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, bookId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    exportChapter(
                        rs.getInt("id"),
                        rs.getString("name"),
                        rs.getString("slug"),
                        rs.getString("description"),
                        bookSlug,
                        bookPath,
                        rs.getTimestamp("updated_at")
                    );
                }
            }
        }
    }
    
    /**
     * Export a single chapter
     */
    private void exportChapter(int chapterId, String name, String slug, String description,
                              String bookSlug, Path bookPath, Timestamp updatedAt) throws Exception {
        chaptersExported++;
        verbose("Exporting chapter: " + name);
        
        String chapterSlug = sanitizeFilename(slug != null ? slug : name);
        Path chapterPath = bookPath.resolve(chapterSlug);
        Files.createDirectories(chapterPath);
        
        // Create chapter start page
        StringBuilder content = new StringBuilder();
        content.append("====== ").append(name).append(" ======\n\n");
        
        if (description != null && !description.isEmpty()) {
            content.append(convertHtmlToDokuWiki(description)).append("\n\n");
        }
        
        content.append("===== Pages =====\n\n");
        
        // List pages in chapter
        String pageSql = "SELECT name, slug FROM pages WHERE chapter_id = ? ORDER BY priority";
        try (PreparedStatement stmt = conn.prepareStatement(pageSql)) {
            stmt.setInt(1, chapterId);
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    String pageSlug = sanitizeFilename(
                        rs.getString("slug") != null ? rs.getString("slug") : rs.getString("name")
                    );
                    content.append("  * [[:")
                           .append(bookSlug)
                           .append(":")
                           .append(chapterSlug)
                           .append(":")
                           .append(pageSlug)
                           .append("|")
                           .append(rs.getString("name"))
                           .append("]]\n");
                }
            }
        }
        
        Path startFile = chapterPath.resolve("start.txt");
        Files.write(startFile, content.toString().getBytes("UTF-8"));
        
        if (preserveTimestamps && updatedAt != null) {
            startFile.toFile().setLastModified(updatedAt.getTime());
        }
        
        // Export all pages in chapter
        exportPagesInChapter(chapterId, chapterPath);
    }
    
    /**
     * Export pages in a chapter
     */
    private void exportPagesInChapter(int chapterId, Path chapterPath) throws Exception {
        String sql = "SELECT id, name, slug, html, created_at, updated_at, created_by " +
                    "FROM pages WHERE chapter_id = ? ORDER BY priority";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, chapterId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    exportPage(
                        rs.getInt("id"),
                        rs.getString("name"),
                        rs.getString("slug"),
                        rs.getString("html"),
                        chapterPath,
                        rs.getTimestamp("created_at"),
                        rs.getTimestamp("updated_at"),
                        rs.getInt("created_by")
                    );
                }
            }
        }
    }
    
    /**
     * Export direct pages (not in chapters)
     */
    private void exportDirectPages(int bookId, Path bookPath) throws Exception {
        String sql = "SELECT id, name, slug, html, created_at, updated_at, created_by " +
                    "FROM pages WHERE book_id = ? AND chapter_id IS NULL ORDER BY priority";
        
        try (PreparedStatement stmt = conn.prepareStatement(sql)) {
            stmt.setInt(1, bookId);
            
            try (ResultSet rs = stmt.executeQuery()) {
                while (rs.next()) {
                    exportPage(
                        rs.getInt("id"),
                        rs.getString("name"),
                        rs.getString("slug"),
                        rs.getString("html"),
                        bookPath,
                        rs.getTimestamp("created_at"),
                        rs.getTimestamp("updated_at"),
                        rs.getInt("created_by")
                    );
                }
            }
        }
    }
    
    /**
     * Export a single page
     * 
     * WARNING: BookStack's HTML is a mess. This converter is better than
     * PHP's version, but manual cleanup may still be required.
     */
    private void exportPage(int pageId, String name, String slug, String html,
                           Path parentPath, Timestamp createdAt, Timestamp updatedAt,
                           int createdBy) throws Exception {
        pagesExported++;
        verbose("Exporting page: " + name);
        
        String pageSlug = sanitizeFilename(slug != null ? slug : name);
        Path pageFile = parentPath.resolve(pageSlug + ".txt");
        
        StringBuilder content = new StringBuilder();
        content.append("====== ").append(name).append(" ======\n\n");
        content.append(convertHtmlToDokuWiki(html));
        
        // Add metadata
        content.append("\n\n/* Exported from BookStack\n");
        content.append("   Original ID: ").append(pageId).append("\n");
        content.append("   Created: ").append(createdAt).append("\n");
        content.append("   Updated: ").append(updatedAt).append("\n");
        content.append("   Author ID: ").append(createdBy).append("\n");
        content.append("*/\n");
        
        Files.write(pageFile, content.toString().getBytes("UTF-8"));
        
        if (preserveTimestamps && updatedAt != null) {
            pageFile.toFile().setLastModified(updatedAt.getTime());
        }
    }
    
    /**
     * Convert BookStack HTML to DokuWiki syntax
     * 
     * This uses JSoup for proper HTML parsing instead of regex.
     * Because parsing HTML with regex is how civilizations collapse.
     */
    private String convertHtmlToDokuWiki(String html) {
        if (html == null || html.isEmpty()) {
            return "";
        }
        
        try {
            Document doc = Jsoup.parse(html);
            StringBuilder wiki = new StringBuilder();
            
            // Remove BookStack's useless custom attributes
            doc.select("[id^=bkmrk-]").removeAttr("id");
            doc.select("[data-*]").removeAttr("data-*");
            
            // Convert recursively
            convertElement(doc.body(), wiki, 0);
            
            // Clean up excessive whitespace
            String result = wiki.toString();
            result = result.replaceAll("\n\n\n+", "\n\n");
            result = result.trim();
            
            return result;
        } catch (Exception e) {
            // If parsing fails, return cleaned HTML
            System.err.println("HTML conversion failed, returning cleaned text: " + e.getMessage());
            return Jsoup.parse(html).text();
        }
    }
    
    /**
     * Convert HTML element to DokuWiki recursively
     * 
     * DON'T SIMPLIFY THIS. It handles edge cases that break other converters.
     */
    private void convertElement(Element element, StringBuilder wiki, int depth) {
        for (org.jsoup.nodes.Node node : element.childNodes()) {
            if (node instanceof org.jsoup.nodes.TextNode) {
                String text = ((org.jsoup.nodes.TextNode) node).text();
                if (!text.trim().isEmpty()) {
                    wiki.append(text);
                }
            } else if (node instanceof Element) {
                Element el = (Element) node;
                String tag = el.tagName().toLowerCase();
                
                switch (tag) {
                    case "h1":
                        wiki.append("\n====== ").append(el.text()).append(" ======\n");
                        break;
                    case "h2":
                        wiki.append("\n===== ").append(el.text()).append(" =====\n");
                        break;
                    case "h3":
                        wiki.append("\n==== ").append(el.text()).append(" ====\n");
                        break;
                    case "h4":
                        wiki.append("\n=== ").append(el.text()).append(" ===\n");
                        break;
                    case "h5":
                        wiki.append("\n== ").append(el.text()).append(" ==\n");
                        break;
                    case "p":
                        convertElement(el, wiki, depth);
                        wiki.append("\n\n");
                        break;
                    case "br":
                        wiki.append("\\\\ ");
                        break;
                    case "strong":
                    case "b":
                        wiki.append("**");
                        convertElement(el, wiki, depth);
                        wiki.append("**");
                        break;
                    case "em":
                    case "i":
                        wiki.append("//");
                        convertElement(el, wiki, depth);
                        wiki.append("//");
                        break;
                    case "u":
                        wiki.append("__");
                        convertElement(el, wiki, depth);
                        wiki.append("__");
                        break;
                    case "code":
                        if (el.parent() != null && el.parent().tagName().equalsIgnoreCase("pre")) {
                            wiki.append("<code>\n").append(el.text()).append("\n</code>\n");
                        } else {
                            wiki.append("''").append(el.text()).append("''");
                        }
                        break;
                    case "pre":
                        // Check if it contains code element
                        Elements codeEls = el.select("code");
                        if (codeEls.isEmpty()) {
                            wiki.append("<code>\n").append(el.text()).append("\n</code>\n");
                        } else {
                            convertElement(el, wiki, depth);
                        }
                        break;
                    case "ul":
                    case "ol":
                        for (Element li : el.select("> li")) {
                            wiki.append("  ".repeat(depth)).append("  * ");
                            convertElement(li, wiki, depth + 1);
                            wiki.append("\n");
                        }
                        break;
                    case "a":
                        String href = el.attr("href");
                        wiki.append("[[").append(href).append("|").append(el.text()).append("]]");
                        break;
                    case "img":
                        String src = el.attr("src");
                        String alt = el.attr("alt");
                        wiki.append("{{").append(src);
                        if (!alt.isEmpty()) {
                            wiki.append("|").append(alt);
                        }
                        wiki.append("}}");
                        break;
                    case "table":
                        // Basic table support
                        for (Element row : el.select("tr")) {
                            for (Element cell : row.select("td, th")) {
                                wiki.append("| ").append(cell.text()).append(" ");
                            }
                            wiki.append("|\n");
                        }
                        wiki.append("\n");
                        break;
                    default:
                        // For unknown tags, just process children
                        convertElement(el, wiki, depth);
                        break;
                }
            }
        }
    }
    
    /**
     * Sanitize filename for filesystem and DokuWiki
     * 
     * CRITICAL: DokuWiki has strict naming requirements.
     * Don't modify this unless you want broken links.
     */
    private String sanitizeFilename(String name) {
        if (name == null || name.isEmpty()) {
            return "unnamed";
        }
        
        // Convert to lowercase (DokuWiki requirement)
        name = name.toLowerCase();
        
        // Replace spaces and special chars with underscores
        name = name.replaceAll("[^a-z0-9_-]", "_");
        
        // Remove multiple consecutive underscores
        name = name.replaceAll("_+", "_");
        
        // Trim underscores from ends
        name = name.replaceAll("^_+|_+$", "");
        
        return name.isEmpty() ? "unnamed" : name;
    }
    
    /**
     * Display export statistics
     */
    private void displayStats() {
        System.out.println();
        System.out.println("Export complete!");
        System.out.println("================================================");
        System.out.println("Books exported: " + booksExported);
        System.out.println("Chapters exported: " + chaptersExported);
        System.out.println("Pages exported: " + pagesExported);
        
        if (errorsEncountered > 0) {
            System.err.println("Errors encountered: " + errorsEncountered);
            System.err.println("Check the error messages above.");
        }
        
        System.out.println();
        System.out.println("Output directory: " + outputPath);
        System.out.println();
        System.out.println("Next steps:");
        System.out.println("1. Copy the exported files to your DokuWiki data/pages/ directory");
        System.out.println("2. Run DokuWiki indexer to rebuild the search index");
        System.out.println("3. Check permissions (DokuWiki needs write access)");
        System.out.println();
        System.out.println("This Java version bypassed PHP entirely. You're welcome.");
    }
    
    /**
     * Log message to console
     */
    private void log(String message) {
        System.out.println(message);
    }
    
    /**
     * Log verbose message
     */
    private void verbose(String message) {
        if (verbose) {
            System.out.println("[VERBOSE] " + message);
        }
    }
}
