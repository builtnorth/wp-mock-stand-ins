<?php
/**
 * Minimal WordPress core class stand-ins for WP_Mock-based PHPUnit tests.
 *
 * WP_Mock (10up/wp_mock) mocks WordPress *functions* via Patchwork, but ships
 * no stand-ins for WordPress *classes* — WP_Error, WP_Query, WP_REST_Request,
 * etc. Every class here is a minimal, deterministic re-implementation of just
 * enough real behavior for a unit test to construct one, call a handful of
 * methods, and assert on the result — not a full reimplementation of
 * WordPress core.
 *
 * Every class is guarded with `class_exists(..., false)` so it never shadows
 * a real WordPress core class if one happens to already be loaded (e.g. a
 * Lane C / wp-phpunit integration test in the same process).
 *
 * @package BuiltNorth\WPMockStandIns
 */

if (! class_exists('WP_Error', false)) {
    class WP_Error {
        private $errors = [];
        private $error_data = [];

        public function __construct($code = '', $message = '', $data = '') {
            if (!empty($code)) {
                $this->errors[$code][] = $message;
                if (!empty($data)) {
                    $this->error_data[$code] = $data;
                }
            }
        }

        public function add($code, $message, $data = '') {
            $this->errors[$code][] = $message;
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }

        public function get_error_code() {
            $codes = array_keys($this->errors);
            return !empty($codes) ? $codes[0] : '';
        }

        public function get_error_data($code = '') {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            return isset($this->error_data[$code]) ? $this->error_data[$code] : null;
        }

        public function get_error_message($code = '') {
            if (empty($code)) {
                $code = $this->get_error_code();
            }
            return isset($this->errors[$code][0]) ? $this->errors[$code][0] : '';
        }

        public function has_errors() {
            return !empty($this->errors);
        }
    }
}

if (! class_exists('WP_REST_Request', false)) {
    class WP_REST_Request {
        private $method = 'GET';
        private $headers = [];
        private $params = [];
        private $route;

        public function get_method() {
            return $this->method;
        }

        public function set_method($method) {
            $this->method = $method;
        }

        public function get_header($header) {
            return $this->headers[$header] ?? null;
        }

        public function set_header($header, $value) {
            $this->headers[$header] = $value;
        }

        public function get_param($param) {
            return $this->params[$param] ?? null;
        }

        public function set_param($param, $value) {
            $this->params[$param] = $value;
        }

        public function get_params() {
            return $this->params;
        }

        public function set_params($params) {
            $this->params = $params;
        }

        /**
         * Routes that accept a JSON body read it through get_json_params().
         * Mockery mocks of this class can only stub methods the class declares,
         * so without this any such route fataled with
         * "Method Mockery_N_WP_REST_Request::get_json_params() does not exist".
         */
        public function get_json_params() {
            return $this->params;
        }

        public function get_body_params() {
            return $this->params;
        }

        public function get_query_params() {
            return $this->params;
        }

        public function get_route() {
            return $this->route ?? '';
        }

        public function set_route($route) {
            $this->route = $route;
        }
    }
}

if (! class_exists('WP_REST_Response', false)) {
    class WP_REST_Response {
        private $data;
        private $status;

        public function __construct($data = null, $status = 200) {
            $this->data = $data;
            $this->status = $status;
        }

        public function get_data() {
            return $this->data;
        }

        public function get_status() {
            return $this->status;
        }
    }
}

if (! class_exists('WP_REST_Server', false)) {
    class WP_REST_Server {
        const READABLE = 'GET';
        const CREATABLE = 'POST';
        const EDITABLE = 'POST, PUT, PATCH';
        const DELETABLE = 'DELETE';
        const ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE';
    }
}

/*
 * Minimal stand-in for WP_Query — real WP core class with no WP_Mock
 * equivalent. Only implements the subset (constructor storing query_vars,
 * get()) that code under test actually reads.
 */
if (! class_exists('WP_Query', false)) {
    class WP_Query {
        public $query_vars = [];
        public $max_num_pages = 0;

        public function __construct($query = '') {
            $this->query_vars = is_array($query) ? $query : [];
        }

        public function get($query_var, $default = '') {
            return $this->query_vars[$query_var] ?? $default;
        }
    }
}

/*
 * Minimal stand-in for WP_Post — real WP core class with no WP_Mock
 * equivalent. Mirrors core's constructor behavior of copying properties
 * from the passed object/array onto itself.
 */
if (! class_exists('WP_Post', false)) {
    #[\AllowDynamicProperties]
    class WP_Post {
        public function __construct($post = []) {
            foreach ((array) $post as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}

/*
 * Minimal stand-in for WP_Post_Type — real WP core class with no WP_Mock
 * equivalent. Only the subset (name, capability object, REST fields,
 * archive/rewrite config) that consuming code reads.
 */
if (! class_exists('WP_Post_Type', false)) {
    class WP_Post_Type {
        public string $name;
        public bool $_builtin = false;
        public string $rest_namespace = '';
        public string $rest_base = '';
        public object $cap;
        public bool $public = true;
        /** @var bool|string */
        public $has_archive = false;
        /** @var array|false */
        public $rewrite = false;

        public function __construct(string $name, string $editCapability = 'edit_posts') {
            $this->name = $name;
            $this->cap = (object) ['edit_posts' => $editCapability];
        }
    }
}

/*
 * Minimal stand-in for WP_Block — real WP core class with no WP_Mock
 * equivalent. Only the block-context array that a dynamic block's render
 * callback typically reads.
 */
if (! class_exists('WP_Block', false)) {
    class WP_Block {
        /** @var array<string, mixed> */
        public array $context = [];
    }
}

/*
 * Minimal stand-ins for WP_Block_Patterns_Registry / WP_Block_Type_Registry /
 * WP_Block_Type — real WP core singletons with no WP_Mock equivalent (WP_Mock
 * only mocks free functions, not classes). Tests populate these directly via
 * get_instance()->register(...) then reset via reset() in tearDown() so
 * state never leaks between tests. Only the subset of behavior consuming
 * code actually uses is implemented — not a full core reimplementation.
 */
if (! class_exists('WP_Block_Patterns_Registry', false)) {
    class WP_Block_Patterns_Registry {
        private static $instance = null;
        private $registered_patterns = [];

        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function register($pattern_name, $pattern_properties) {
            $pattern_properties['name'] = $pattern_name;
            $this->registered_patterns[$pattern_name] = $pattern_properties;
            return true;
        }

        public function unregister($pattern_name) {
            unset($this->registered_patterns[$pattern_name]);
        }

        public function get_registered($pattern_name) {
            return $this->registered_patterns[$pattern_name] ?? null;
        }

        public function get_all_registered($outside_init_only = false) {
            return array_values($this->registered_patterns);
        }

        public function is_registered($pattern_name) {
            return isset($this->registered_patterns[$pattern_name]);
        }

        public function reset() {
            $this->registered_patterns = [];
        }
    }
}

if (! class_exists('WP_Block_Type', false)) {
    class WP_Block_Type {
        public $name;
        public $title = '';
        public $category = null;
        public $description = '';
        public $keywords = [];
        public $attributes = [];
        public $allowed_blocks = null;
        public $parent = null;

        public function __construct($block_type, $args = []) {
            $this->name = $block_type;
            foreach ($args as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}

if (! class_exists('WP_Block_Type_Registry', false)) {
    class WP_Block_Type_Registry {
        private static $instance = null;
        private $registered_block_types = [];

        public static function get_instance() {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function register($name, $args = []) {
            $block_type = $name instanceof WP_Block_Type ? $name : new WP_Block_Type($name, $args);
            $this->registered_block_types[$block_type->name] = $block_type;
            return $block_type;
        }

        public function unregister($name) {
            unset($this->registered_block_types[$name]);
        }

        public function get_registered($name) {
            return $this->registered_block_types[$name] ?? null;
        }

        public function get_all_registered() {
            return $this->registered_block_types;
        }

        public function is_registered($name) {
            return isset($this->registered_block_types[$name]);
        }

        public function reset() {
            $this->registered_block_types = [];
        }
    }
}

/**
 * Minimal $wpdb stand-in.
 *
 * Tests that only need `$wpdb->prefix` have historically assigned a bare
 * stdClass. That breaks the moment the code under test reaches a *method* —
 * a package whose boot path installs a DB schema calls
 * get_charset_collate(), and stdClass fatals with "call to undefined method".
 *
 * This covers the read-only surface a unit test can meaningfully assert on and
 * makes the mutating calls inert: queries return empty rather than pretending
 * to have run. A test that needs real query results should stub the specific
 * method on an instance, or belong in an integration suite with a real
 * database.
 */
if (! class_exists('wpdb', false)) {
    class wpdb {
        public $prefix = 'wp_';
        public $base_prefix = 'wp_';
        public $insert_id = 1;
        public $last_error = '';
        public $num_rows = 0;
        public $options = 'wp_options';
        public $posts = 'wp_posts';
        public $postmeta = 'wp_postmeta';
        public $users = 'wp_users';
        public $usermeta = 'wp_usermeta';

        /** @var list<string> Every query this instance was asked to run. */
        public $queries = [];

        public function get_charset_collate() {
            return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci';
        }

        /**
         * Interpolates like the real prepare() for the placeholders WordPress
         * supports, so assertions on the built SQL are meaningful. Not a
         * security boundary — this is a test double.
         */
        public function prepare($query, ...$args) {
            if ($args === []) {
                return $query;
            }
            if (count($args) === 1 && is_array($args[0])) {
                $args = $args[0];
            }
            $out = '';
            $i = 0;
            $len = strlen($query);
            while ($i < $len) {
                $ch = $query[$i];
                if ($ch === '%' && $i + 1 < $len && in_array($query[$i + 1], ['s', 'd', 'f'], true)) {
                    $type = $query[$i + 1];
                    $val = array_shift($args);
                    if ($type === 'd') {
                        $out .= (int) $val;
                    } elseif ($type === 'f') {
                        $out .= (float) $val;
                    } else {
                        $out .= "'" . str_replace("'", "\\'", (string) $val) . "'";
                    }
                    $i += 2;
                    continue;
                }
                $out .= $ch;
                ++$i;
            }
            return $out;
        }

        public function query($query) {
            $this->queries[] = (string) $query;
            return 0;
        }

        public function get_var($query = null, $x = 0, $y = 0) {
            if (null !== $query) {
                $this->queries[] = (string) $query;
            }
            return null;
        }

        public function get_row($query = null, $output = 'OBJECT', $y = 0) {
            if (null !== $query) {
                $this->queries[] = (string) $query;
            }
            return null;
        }

        public function get_col($query = null, $x = 0) {
            if (null !== $query) {
                $this->queries[] = (string) $query;
            }
            return [];
        }

        public function get_results($query = null, $output = 'OBJECT') {
            if (null !== $query) {
                $this->queries[] = (string) $query;
            }
            return [];
        }

        public function insert($table, $data, $format = null) {
            return 1;
        }

        public function update($table, $data, $where, $format = null, $where_format = null) {
            return 1;
        }

        public function delete($table, $where, $where_format = null) {
            return 1;
        }

        public function esc_like($text) {
            return addcslashes((string) $text, '_%\\');
        }

        public function has_cap($db_cap) {
            return true;
        }
    }
}
