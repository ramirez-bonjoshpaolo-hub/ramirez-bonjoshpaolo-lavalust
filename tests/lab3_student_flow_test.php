<?php

$root = dirname(__DIR__);

function expect_true($condition, $message)
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

function file_text($root, $relative_path)
{
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative_path);
    expect_true(is_file($path), "missing {$relative_path}");
    return file_get_contents($path);
}

$controller = file_text($root, 'app/controllers/StudentController.php');
$middleware = file_text($root, 'app/middlewares/StudentMiddleware.php');
$routes = file_text($root, 'app/config/routes.php');
$middleware_config = file_text($root, 'app/config/middleware.php');
$autoload = file_text($root, 'app/config/autoload.php');
$home_view = file_text($root, 'app/views/student/index.php');
$profile_view = file_text($root, 'app/views/student/profile.php');

expect_true(str_contains($controller, 'class StudentController extends Controller'), 'controller class missing');
expect_true(str_contains($controller, 'function index'), 'controller index method missing');
expect_true(str_contains($controller, 'function profile'), 'controller profile method missing');
expect_true(str_contains($controller, "'name' => getenv('STUDENT_NAME') ?: 'Bon Josh Paolo Ramirez'"), 'student name missing');
foreach (['STUDENT_ID', 'STUDENT_COURSE', 'STUDENT_YEAR_LEVEL', 'STUDENT_SECTION', 'STUDENT_EMAIL'] as $field) {
    expect_true(str_contains($controller, $field), "{$field} configuration missing");
}
expect_true(str_contains($routes, "'/student', 'StudentController::index'"), 'student route missing');
expect_true(str_contains($routes, "'/student/profile', 'StudentController::profile'"), 'profile route missing');
expect_true(str_contains($routes, "->middleware('student_access')"), 'profile middleware attachment missing');
expect_true(str_contains($middleware_config, "'student_access'"), 'middleware alias missing');
expect_true(str_contains($middleware, 'class StudentMiddleware'), 'middleware class missing');
expect_true(str_contains($middleware, 'function handle'), 'middleware handle method missing');
expect_true(str_contains($home_view, 'Student Information'), 'home heading missing');
expect_true(str_contains($profile_view, 'Student Profile'), 'profile heading missing');
expect_true(str_contains($home_view, 'site_url('), 'home navigation helper missing');
expect_true(str_contains($profile_view, 'site_url('), 'profile navigation helper missing');
expect_true(str_contains($autoload, "'url'"), 'URL helper is not autoloaded');

echo "PASS: Lab 3 student flow structure is present.\n";
