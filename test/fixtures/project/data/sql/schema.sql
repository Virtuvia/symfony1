CREATE TABLE sortable_article (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255), position INTEGER);
CREATE TABLE sortable_article_category (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(100));
CREATE TABLE sortable_article_unique_by (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255), category_id INTEGER UNSIGNED NOT NULL, position INTEGER);
CREATE UNIQUE INDEX sortable_article_position_sortable_idx ON sortable_article (position);
CREATE UNIQUE INDEX sortable_article_unique_by_position_sortable_idx ON sortable_article_unique_by (position, category_id);
