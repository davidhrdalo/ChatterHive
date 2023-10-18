-- SQL file to create a database for a basic social media site
-- Creating the User table
CREATE TABLE IF NOT EXISTS user (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);

-- Creating the Post table
CREATE TABLE IF NOT EXISTS post (
    post_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT,
    message TEXT,
    date TEXT,
    user_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Creating the Like table
CREATE TABLE IF NOT EXISTS post_like (
    post_like_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    post_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (post_id) REFERENCES post(post_id)
);

-- Creating the Comment table
CREATE TABLE IF NOT EXISTS comment (
    comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    message TEXT,
    date TEXT,
    post_id INTEGER,
    user_id INTEGER,
    FOREIGN KEY (post_id) REFERENCES post(post_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Creating the comment comment table
CREATE TABLE IF NOT EXISTS comment_comment (
    comment_comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    message TEXT,
    date TEXT,
    user_id INTEGER,
    comment_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (comment_id) REFERENCES comment(comment_id)
);

-- Insert data into User table
INSERT INTO user (name) VALUES 
('Alice'),
('Bob'),
('Dave'),
('Steve'),
('John'),
('Julia'),
('Riana'),
('Charlie');

-- Insert data into Post table
INSERT INTO post (title, message, date, user_id) VALUES 
('My First Post', 'This is the content of my first post.', '2023-09-01 12:00:00', 1),
('A Day in the Life', 'My experiences and adventures.', '2023-09-02 14:30:00', 2),
('A tests Life', 'My !!!!!!! and adventures.', '2023-09-03 16:45:00', 2),
('A Day', 'My adventures.', '2023-09-03 18:15:00', 3),
('Nature and its beauty', 'Nature is truly mesmerizing.', '2023-09-04 09:00:00', 4),
('Tech in 2023', 'Discussing the latest tech trends.', '2023-09-05 10:15:00', 5),
('Cooking 101', 'Basics of cooking for beginners.', '2023-09-06 11:30:00', 6),
('Travel Diary', 'My experiences traveling the world.', '2023-09-07 12:45:00', 7);

-- Insert data into Comment table
INSERT INTO comment (message, date, post_id, user_id) VALUES 
('Great post, Alice!', '2023-09-03 12:00:00', 1, 3),
('Terible post, Alice!', '2023-09-03 14:30:00', 1, 2),
('Interesting read, Bob!', '2023-09-03 16:45:00', 2, 3),
('Love your insights on tech!', '2023-09-05 10:45:00', 5, 1),
('I would love to travel like you do, Julia!', '2023-09-07 13:00:00', 7, 2),
('Cooking is such an art. Thanks for the tips!', '2023-09-06 11:50:00', 6, 3),
('Nature is truly breathtaking.', '2023-09-04 09:20:00', 4, 7);

-- Insert data into Post_Like table
INSERT INTO post_like (user_id, post_id) VALUES 
(1, 2),
(3, 1),
(4, 1),
(5, 2),
(6, 3),
(7, 4),
(8, 5),
(5, 6),
(6, 7);

-- Insert data into comment_comment table
INSERT INTO comment_comment (message, date, user_id, comment_id) VALUES 
('Great Comment, Alice!', '2023-09-03 13:00:00', 3, 1),
('Terible Comment, Alice!', '2023-09-03 14:45:00', 2, 1),
('Interesting Comment, Bob!', '2023-09-03 17:00:00', 3, 2),
('I totally agree with you!', '2023-09-05 11:00:00', 5, 4),
('Yes, nature is truly wonderful.', '2023-09-04 09:30:00', 8, 7),
('Haha, I feel the same about cooking.', '2023-09-06 12:00:00', 4, 6),
('Wanderlust is real!', '2023-09-07 13:15:00', 3, 6);