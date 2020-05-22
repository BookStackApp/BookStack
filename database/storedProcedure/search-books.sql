DELIMITER //
CREATE PROCEDURE `search_book`(
	IN v_search_query VARCHAR(100),
    IN v_limit INT
)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS matchEntitiesView;

	CREATE TEMPORARY TABLE matchEntitiesView 
		SELECT
			st.entity_type AS morph_class,
			entity_id,
            entity_type,
			SUM(st.score) AS score
		FROM search_terms AS st
		WHERE MATCH (st.term) AGAINST (v_search_query)
		GROUP BY entity_id, morph_class
		ORDER BY score DESC
        LIMIT v_limit;
    
    SELECT
		m.score AS ranking,
		b.id AS bookID, b.name AS bookName, b.slug AS bookSlug, b.description AS bookDescription,
        bs.id AS bookShelvesID, bs.name AS bookShelvesName, bs.slug AS bookShelvesSlug, bs.description AS bookShelvesDescription,
        p.id AS pageID, p.name AS pageName, p.slug AS pageSlug, p.text AS pagetext,
        (SELECT books.slug FROM books WHERE books.id = p.book_id) AS pageBookSlug,
        (SELECT chapters.slug FROM chapters WHERE chapters.id = p.chapter_id) AS pageChapterSlug,
        ch.id AS chapterID, ch.id AS chapterID, ch.name AS chapterName, ch.slug AS chapterSlug, ch.description AS chapterDescription,
		(SELECT books.slug FROM books WHERE books.id = ch.book_id) AS chapterBookSlug
        FROM
			matchEntitiesView m
			LEFT JOIN books AS b
			ON b.id = m.entity_id AND m.morph_class LIKE '%Book'
            LEFT JOIN bookshelves AS bs
			ON bs.id = m.entity_id AND m.morph_class LIKE '%Bookshelf'
			LEFT JOIN pages AS p
            ON p.id = m.entity_id AND m.morph_class LIKE '%Page'
			LEFT JOIN chapters AS ch
            ON ch.id = m.entity_id AND m.morph_class LIKE '%Chapter';
            	
    DROP TEMPORARY TABLE matchEntitiesView;
END //
DELIMITER ;