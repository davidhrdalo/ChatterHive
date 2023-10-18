<?php

use Illuminate\Support\Facades\Route;

/*--------------------------------------------------------------------------
|Web Routes
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|--------------------------------------------------------------------------*/

/*  Route for home page.
    Page displays all posts on the site, posts are displayed with:
    post title, author name, date, direct comment count, all comment count, like count */
Route::get('/', function(){

    $sql = "SELECT P.post_id, P.title, P.message, P.date, P.user_id, U.name,
            -- Subquery to count the direct comments for the post
            (SELECT COUNT(*) FROM Comment AS C WHERE P.post_id = C.post_id) AS comment_count,
            -- Subquery to count all comments associated with the post
            ((SELECT COUNT(*) FROM Comment AS C WHERE P.post_id = C.post_id)+
            (SELECT COUNT(*) FROM comment_comment AS CC WHERE CC.comment_id IN 
            (SELECT C.comment_id FROM Comment C WHERE P.post_id = C.post_id))) AS all_comment_count,
            -- Subquery to count all likes for the post
            (SELECT COUNT(*) FROM Post_like as L WHERE P.post_id = L.post_id) AS like_count

            FROM Post as P, User as U
            WHERE P.user_id = U.user_id
            ORDER BY P.date desc";
    
    $posts = DB::select($sql); 

    return view('post.post-list')->with('posts', $posts);
});


/*  Route to clear all Laravel session data (name) 
    Route can be used by users who wish to "logout" */
Route::get('clear-session', function() {

    // No validation required as this simply flushes ths session data
    Session::flush(); 

    // Redirects user back to the previous page
    return redirect()->back()->with('universal_message', 'Logged out successfully!');
});


/*  Route for users page 
    Page shows all unuiqe usuers who have made posts*/
Route::get('users', function(){

    // Filters users for only those who have made posts
    $sql = "SELECT name, user_id
            FROM User as U
            WHERE EXISTS
            (SELECT *
            FROM Post as P
            WHERE P.user_id = U.user_id)";

    $users = DB::select($sql);
    
    return view('user.user-list')->with('users', $users);
});


/*  Route for login/register page 
    Users can log into existing accounts or create a new one */
Route::get('login', function(){

    return view('user.user-login');
});


/*  Route for user detail page 
    Page displays users name at the top and all posts made by the user*/
Route::get('user-details/{id}', function($id){

    $user_posts = get_user_posts($id);

    $sql = "SELECT U.name as name
            FROM User as U
            WHERE U.user_id = ?";
    
    $name = DB::select($sql, [$id])[0]->name;

    return view('user.user-detail')->with('user_posts', $user_posts)->with('name', $name);
});


/* Function for user posts page */
function get_user_posts($id){

    $sql = "SELECT P.post_id, P.title, P.message, P.date
            FROM Post as P
            WHERE P.user_id=?";

    $user_posts = DB::select($sql, [$id]);

    return $user_posts;
};


/*  Route for post detail page 
    Page displays title, date, author, message, comments, comment comments, likes
    Forms and links to allow users to like, comment, edit, and delete the post*/
Route::get('post-detail/{id}', function($id){

    $post = get_post($id);
    $likes = get_post_like($id);
    $comments = get_comment($id);

    // Initialise empty array
    $comment_comments = [];
    // Loop through comment array
    foreach ($comments as $comment) {
        // comment_id is taken as a key in the comment_comments array
        $comment_comments[$comment->comment_id] = get_comment_comment($comment->comment_id);
    }

    $user_has_liked = user_has_like($id);

    return view('post.post-detail')
        ->with('post', $post)
        ->with('comments', $comments)
        ->with('likes', $likes)
        ->with('comment_comments', $comment_comments)
        ->with ('user_has_liked', $user_has_liked);
});


/*  function for getting post details content with the post */
function get_post($id){

    $sql = "SELECT P.post_id, P.title, P.message, P.date, U.name, P.user_id
            FROM Post as P, User as U
            WHERE P.post_id=?
            AND P.user_id = U.user_id";
    
    $posts = DB::select($sql, [$id]);

    return $post = $posts[0];
};


/*  Function to get the amount of post likes */
function get_post_like($id){
    $sql = "SELECT COUNT(*) as likes_count
            FROM post_like
            WHERE post_id = ?";

    $likes = DB::select($sql, [$id]);

    return $likes[0]->likes_count;
};


/*  function for getting comments for the specific post */
function get_comment($id){
    $sql = "SELECT U.user_id, U.name, C.post_id, C.message, C.date, C.user_id, C.comment_id,
            (SELECT name FROM user as U WHERE U.user_id = C.user_id) as name
            FROM Comment as C, User as U
            WHERE C.post_id=?
            AND C.user_id = U.user_id
            ORDER BY C.date DESC";

    return $comments = DB::select($sql, [$id]);
};


/*  Route for getting comments on comments */
function get_comment_comment($comment_id) {
    $sql = "SELECT CC.message as c_message, CC.date as date, CC.user_id as user_id,
            (SELECT name FROM user as U WHERE U.user_id = CC.user_id) as name
            FROM comment_comment AS CC
            WHERE CC.comment_id = ?
            ORDER BY CC.date DESC";

    return DB::select($sql, [$comment_id]);
};


/*  Function to check if a usuer exists or create one */
function get_or_create_user($name) {
    // Check if user exists
    $sql_check = "SELECT user_id FROM User WHERE name = ?";
    $user = DB::select($sql_check, [$name]);

        // If user is not empty (exists), return the user's ID
        if (!empty($user)) {
            return $user[0]->user_id;
        }
        
        // If the user does not exists create a new user
        $sql_insert = "INSERT INTO User (name) VALUES (?)";
        return DB::insert($sql_insert, [$name]);
}


/*  Route for 'form-post-create' form
    Form allows users to create a new post with a author, title and message */
Route::post('post-create-action', function(){
    $title = request('title');
    // Validation check that scans the string length to ensure post is at least 3 chars long
    if(strlen($title) < 3 ){
        return redirect()->back()->with('post_message', 'The title must be at least 3 characters long.');
    }

    $message = request('message');
    // Validation check that counts the strings to ensure the message has at least 5 words
    $wordCount = str_word_count($message);
    if($wordCount < 5) {
        return redirect()->back()->with('post_message', 'The message must have at least 5 words.');
    }

    $name = request('name');
    // Validation check that pattern matches 0-9 to ensure the name does not contain numbers
    if (preg_match('/\d/', $name)) {
        return redirect()->back()->with('post_message', 'The name must not contain numbers.');
    }

    $user_id = get_or_create_user($name);

    $id = new_post($title, $message, $name);

    if ($id){
        // Store name in session then redirect back to the home page
        Session::put('name', $name);
        return redirect(url("/"))->with('post_message', 'Post created successfully!');
    } else{
        return redirect()->back()->with('post_message', 'Could not create post');
    };
});

/*  Function to create a new post
    Function takes title, author and message and creates a new row of data in the post table */
function new_post($title, $message, $name){

    $sqluser = "SELECT user_id 
                FROM user
                WHERE name = ?";

    $result = DB::select($sqluser, [$name]);

    $user_id = $result[0]->user_id;

    $sql = "INSERT into Post (date, title, message, user_id) 
            VALUES (datetime('now'), ?, ?, ?)";

    return DB::insert($sql, [$title, $message, $user_id]);
};


/*  Route to get to the edit page */
Route::get('post-edit/{id}', function($id){
    // Action requires user to be logged in
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }
    $post = get_post($id);

    return view('post.post-edit')->with('post', $post);
});


/*  Route for the 'form-post-edit' form
    Form allows users to edit the title and message of a post */
Route::post('post-edit-action', function(){
    // Action requires user to be logged in
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }
    // post_id is a hidden feild in the form and is submited with the rest of the data
    $post_id = request('post_id');

    $title = request('title');
    // Validation check that scans the string length to ensure post is at least 3 chars long
    if(strlen($title) < 3 ){
        return redirect()->back()->with('edit_message', 'The title must be at least 3 characters long.');
    }

    $message = request('message');
    // Validation check that counts the strings to ensure the message has at least 5 words
    $wordCount = str_word_count($message);
    if($wordCount < 5) {
        return redirect()->back()->with('edit_message', 'The message must have at least 5 words.');
    }

    $id = edit_post($post_id, $title, $message);

    if ($id){
        return redirect(url("post-detail/$post_id"))->with('detail_message', 'Post edited successfully!');
    } else{
        return redirect()->back()->with('edit_message', 'Could not edit post.');
    };
});


/*  Function to update the database with what was collected in the 'post-edit-action' route */
function edit_post($post_id, $title, $message){

    $sql = "UPDATE post
            SET date = datetime('now'), title = ?, message = ?
            WHERE post_id = ?";

    return DB::update($sql, [$title, $message, $post_id]);
};

/*  Route for the 'form-post-delete' form
    Form allows users to delete a post and all relevant data with it */
Route::post('post-delete-action', function(){
    // Action requires user to be logged in
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }

    $post_id = request('post_id');

    $id = delete_post($post_id);

    return redirect('/')->with('post_message', 'Post deleted successfully!');

});


/* Function to delete the post and all relevent data from the database */
function delete_post($post_id){
    //All comment_comments are removed
    $sql_comment_comments = "DELETE FROM comment_comment
                            WHERE comment_id IN
                            (SELECT comment_id
                            FROM comment
                            WHERE post_id = ?)";
    
    DB::delete($sql_comment_comments, [$post_id]);
    
    //All comments are removed
    $sql_comments = "DELETE FROM comment
                    WHERE post_id = ?";
    
    DB::delete($sql_comments, [$post_id]);

    //All likes are removed
    $sql_likes = "DELETE FROM post_like
                WHERE post_id = ?";
   
   DB::delete($sql_likes, [$post_id]);

    //The post is removed
    $sql_post = "DELETE FROM post
                WHERE post_id = ?";

    DB::delete($sql_post, [$post_id]);
};


/* Function to check if the user has liked a post */
function user_has_like($id){
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }

    $name = Session::get('name');

    $sql = "SELECT user_id 
            FROM user 
            WHERE name = ?";

    $results = DB::select($sql, [$name]);

    $user_id = $results[0]->user_id;

    $sql = "SELECT * FROM post_like WHERE user_id = ? AND post_id = ?";
    $like_result = DB::select($sql, [$user_id, $id]);

    // Checking like_result if there is a record in the database of a like
    // Returns true if the record is not empty
    return !empty($like_result);
};


/*  Route for 'form-post-like' form
    Form allows users to like a post */
Route::post('post-like-action', function(){
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }

    $name = Session::get('name');

    $sql = "SELECT user_id 
            FROM user 
            WHERE name = ?";

    $results = DB::select($sql, [$name]);

    $user_id = $results[0]->user_id;

    $post_id = request('post_id');

    $id = post_like($user_id, $post_id);

    if ($id) {
        return back()->with('detail_message', 'Post liked successfully!');
    } else {
        return back()->with('detail_message', 'Unable to like post.');
    }
});


/* Function to add a like to the database */
function post_like($user_id, $post_id){

    $sql = "INSERT into post_like (user_id, post_id) 
            VALUES (?, ?)";

    return DB::insert($sql, [$user_id, $post_id]);
};


/*  Route for 'form-post-unlike' form
    Form is used for users to remove a like from a post */
Route::post('post-unlike-action', function(){
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }

    $name = Session::get('name');

    $sql = "SELECT user_id 
            FROM user 
            WHERE name = ?";

    $results = DB::select($sql, [$name]);

    $user_id = $results[0]->user_id;

    $post_id = request('post_id');

    $id = post_unlike($user_id, $post_id);

    if ($id) {
        return back()->with('detail_message', 'Like removed successfully!');
    } else {
        return back()->with('detail_message', 'Unable to remove like.');
    }
});


/* Function to delete a like for a post from the database */
function post_unlike($user_id, $post_id){
    $sql = "DELETE FROM post_like 
            WHERE user_id = ? AND post_id = ?";
    
    return DB::delete($sql, [$user_id, $post_id]);
};


/*  Route to for 'form-post-comment' for
    Form allows users to create comments for a specific post */
Route::post('post-comment-action', function(){

    $message = request('message');
    // Validation check that counts the strings to ensure the message has at least 1 word
    $wordCount = str_word_count($message);
    if($wordCount < 1) {
        return redirect()->back()->with('detail_message', 'The message must have at least 1 word.');
    }

    $name = request('name');
    // Validation check that pattern matches 0-9 to ensure the name does not contain numbers
    if (preg_match('/\d/', $name)) {
        return redirect()->back()->with('post_message', 'The name must not contain numbers.');
    }

    // Creates an account if required
    $user_id = get_or_create_user($name);

    $post_id = request('post_id');

    $id = post_comment($message, $name, $post_id);

    if ($id){
        Session::put('name', $name);
        return back()->with('detail_message', 'Comment added successfully!');
    } else{
        return back()->with('detail_message', 'Unable to add comment.');
    };
});


/* Function for adding post comment data into the database */
function post_comment($message, $name, $post_id){

    $sqluser = "SELECT user_id 
    FROM user
    WHERE name = ?";

    $result = DB::select($sqluser, [$name]);

    $user_id = $result[0]->user_id;

    $sql = "INSERT into comment (date, message, user_id, post_id) 
            VALUES (datetime('now'), ?, ?, ?)";

    return DB::insert($sql, [$message, $user_id, $post_id]);
};


/*  Route for "form-comment-comment" form
    Form can be used to add a comment to a comment */
Route::post('comment-comment-action', function(){
    // Check if the session name is empty, redirect to login if it is
    if (empty(Session::get('name'))) {
        return redirect('login');
    }

    $name = Session::get('name');

    $message = request('message');
    // Validation check that counts the strings to ensure the message has at least 1 word
    $wordCount = str_word_count($message);
    if($wordCount < 1) {
        return redirect()->back()->with('detail_message', 'The message must have at least 1 word.');
    }
    
    $comment_id = request('comment_id');

    $id = new_comment_comment($message, $name, $comment_id);

    if ($id){
        Session::put('name', $name);
        return back()->with('detail_message', 'Comment added successfully!');
    } else{
        return back()->with('deail_message', 'Unable to add comment.');
    };
});


/* Function to add a comment to a comment */
function new_comment_comment($message, $name, $comment_id){
    // Finding user_id associated with the name entered
    $sqluser = "SELECT user_id 
                FROM user
                WHERE name = ?";

    $user_id = DB::select($sqluser, [$name]);

    // Selecting the first result from the array
    $user_id = $user_id[0]->user_id;

    $sql = "INSERT into comment_comment (date, message, user_id, comment_id) 
            VALUES (datetime('now'), ?, ?, ?)";

    return DB::insert($sql, [$message, $user_id, $comment_id]);
};


/*  Route for "form-user-login" form
    Form can be used to login or create a user */
Route::post('login_action', function(){
    $name = request('name');

    // Validation check to ensure name has no numbers
    if (preg_match('/\d/', $name)) {
        return redirect()->back()->with('login_message', 'The name must not contain numbers.');
    }

    // Using get_or_create_user function that has been defined above
    $user_id = get_or_create_user($name);

    if ($user_id){
        // Storing name in the Laravel session
        Session::put('name', $name);
        // Using the form to capture the previous URL and redirect back. '/' used as a fallback
        $previousURL = request('previous_url', '/');
        return redirect($previousURL)->with('universal_message', 'Logged in successfully!');
    } else{
        return redirect()->back()->with('login_message', 'Could not login or create user.');
    };
});