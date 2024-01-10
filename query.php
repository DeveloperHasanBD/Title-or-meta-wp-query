// This code need to use on custom search.php // page template //

$search_val = $_GET['search_val'] ?? '';
$args = [
	'post_type'  => 'product',
	'posts_per_page' => -1,
	'_meta_or_title' => $search_val,   // Our new custom argument!
	'meta_query'    => [
		[
			'key'     => 'code',
			'value'   => $search_val,
			'compare' => 'like'
		]
	],
];
$query = new WP_Query($args);



// This code need to paste on functions.php

add_action('pre_get_posts', function ($q) {
    if ($title = $q->get('_meta_or_title')) {
        add_filter('get_meta_sql', function ($sql) use ($title) {
            global $wpdb;

            // Only run once:
            static $nr = 0;
            if (0 != $nr++) return $sql;

            // Modify WHERE part:
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare("{$wpdb->posts}.post_title LIKE '%s'", '%'.$title.'%'),,
                mb_substr($sql['where'], 5, mb_strlen($sql['where']))
            );
            return $sql;
        });
    }
});

