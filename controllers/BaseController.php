<?php

/**
 * @file controllers/BaseController.php
 * @brief Abstract base class for all application controllers.
 *
 * Provides common functionalities such as database access, application configuration,
 * view rendering, redirection, flash message handling, and input sanitization.
 */
abstract class BaseController {
    /**
     * @var PDO $pdo The PDO database connection instance.
     */
    protected PDO $pdo;

    /**
     * @var array $appConfig The application's configuration settings.
     */
    protected array $appConfig;

    /**
     * Constructor for BaseController.
     * Initializes the PDO database connection and application configuration.
     *
     * @param PDO $pdo The PDO database connection object.
     * @param array $appConfig Application configuration settings.
     */
    public function __construct(PDO $pdo, array $appConfig) {
        $this->pdo = $pdo;
        $this->appConfig = $appConfig;
    }

    /**
     * Loads and renders a view file within the application's header and footer.
     *
     * This method:
     * 1. Extracts data for the view, making it accessible as local variables.
     * 2. Includes the global header file (`header.php`).
     * 3. Includes the specific content view file (e.g., `views/kos/daftar.php`).
     * 4. Includes the global footer file (`footer.php`).
     * 5. Provides fallback HTML for missing header/footer/view files in development,
     * and logs errors for production.
     *
     * @param string $viewName The name of the view file (e.g., 'home/index', 'kos/daftar').
     * Expects format like 'folder/file' (e.g., 'kos/daftar').
     * @param array $data An associative array of data to pass to the view.
     * @param string|null $pageTitle An optional title for the HTML page. If null, a default is used.
     * @return void
     */
    protected function loadView(string $viewName, array $data = [], ?string $pageTitle = null): void {
        // Extract data array into local variables for the view's scope.
        // E.g., ['user' => $user_data] becomes $user accessible in the view.
        extract($data);

        // Make appConfig available directly in the view's scope as well.
        $appConfig = $this->appConfig;

        // Construct full paths to header, view, and footer files.
        $headerPath = $appConfig['INCLUDES_PATH'] . 'header.php';
        // Replace potential dot notation (e.g., 'kos.daftar') with directory separator.
        $viewPath   = $appConfig['VIEWS_PATH'] . str_replace('.', DIRECTORY_SEPARATOR, $viewName) . '.php';
        $footerPath = $appConfig['INCLUDES_PATH'] . 'footer.php';

        // --- View Inclusion Order ---

        // 1. Load the global header
        if (file_exists($headerPath)) {
            require_once $headerPath;
        } else {
            // Fallback HTML for missing header (for development visibility and basic page structure)
            error_log("BaseController Warning: Header file '{$headerPath}' not found. Rendering basic HTML head.");
            echo "<!DOCTYPE html><html lang=\"id\"><head><meta charset=\"UTF-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"><title>" . htmlspecialchars($pageTitle ?? $appConfig['APP_NAME'] ?? 'Aplikasi') . "</title></head><body>";
            // In a production environment, you might want to redirect to an error page or show a generic message.
        }

        // 2. Load the specific content view
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Display an error message directly on the page for missing views
            error_log("BaseController Warning: View file '{$viewPath}' not found. Displaying error message.");
            http_response_code(404); // Set HTTP status code to Not Found
            echo "<div style='background-color:#ffe0e0; color:#c00; padding:15px; border:1px solid #c00; border-radius:5px; margin:20px; text-align:center;'>";
            echo "<h2>Error 404: Halaman Tidak Ditemukan</h2>";
            echo "<p>View '<strong>" . htmlspecialchars($viewName) . "</strong>' tidak ditemukan.</p>";
            echo "<p>Silakan periksa path file view Anda.</p>";
            echo "</div>";
        }

        // 3. Load the global footer
        if (file_exists($footerPath)) {
            require_once $footerPath;
        } else {
            // Fallback HTML for missing footer
            error_log("BaseController Warning: Footer file '{$footerPath}' not found. Closing HTML body.");
            echo "</body></html>";
        }
    }

    /**
     * Redirects the browser to a specified URL.
     *
     * @param string $path The path to redirect to. Can be relative (e.g., 'dashboard')
     * or a full URL (e.g., 'http://example.com/other-page').
     * @return void
     */
    protected function redirect(string $path): void {
        $url = $path;
        // If the path is not already an absolute URL, prepend the base URL.
        if (!(strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0)) {
            $url = rtrim($this->appConfig['BASE_URL'], '/') . '/' . ltrim($path, '/');
        }
        // Send the HTTP Location header for redirection.
        header("Location: " . $url);
        exit; // Terminate script execution after redirection.
    }

    /**
     * Sets a flash message to be displayed on the next page load.
     * Stores the message and its type in the session.
     *
     * @param string $message The message content.
     * @param string $type The type of message (e.g., 'info', 'success', 'error', 'warning').
     * @return void
     */
    protected function setFlashMessage(string $message, string $type = 'info'): void {
        // Only set flash message if a session is active.
        if (session_status() == PHP_SESSION_ACTIVE) {
            $_SESSION['flash_message'] = ['message' => $message, 'type' => $type];
        } else {
            error_log("BaseController Warning: Attempted to set flash message, but no session is active. Message: '{$message}'");
        }
    }

    /**
     * Retrieves and sanitizes input from the POST request.
     *
     * @param string $key The name of the POST field.
     * @param mixed $default The default value to return if the key is not found or filtering fails.
     * @param int $filter The filter constant to apply (e.g., FILTER_SANITIZE_STRING, FILTER_VALIDATE_INT).
     * @param mixed $options Options for the filter (e.g., FILTER_FLAG_STRIP_LOW).
     * @return mixed The filtered value, or the default value if not found/invalid.
     */
    protected function getInputPost(string $key, $default = null, int $filter = FILTER_DEFAULT, $options = 0) {
        $value = filter_input(INPUT_POST, $key, $filter, $options);
        // Special handling for FILTER_VALIDATE_BOOLEAN as it can return false legitimately.
        return ($value === null || ($value === false && $filter !== FILTER_VALIDATE_BOOLEAN)) ? $default : $value;
    }

    /**
     * Retrieves and sanitizes input from the GET request.
     *
     * @param string $key The name of the GET field.
     * @param mixed $default The default value to return if the key is not found or filtering fails.
     * @param int $filter The filter constant to apply (e.g., FILTER_SANITIZE_URL, FILTER_VALIDATE_INT).
     * @param mixed $options Options for the filter.
     * @return mixed The filtered value, or the default value if not found/invalid.
     */
    protected function getInputGet(string $key, $default = null, int $filter = FILTER_DEFAULT, $options = 0) {
        $value = filter_input(INPUT_GET, $key, $filter, $options);
        // Special handling for FILTER_VALIDATE_BOOLEAN as it can return false legitimately.
        return ($value === null || ($value === false && $filter !== FILTER_VALIDATE_BOOLEAN)) ? $default : $value;
    }

    /**
     * Checks if a user is currently logged in.
     *
     * @return bool True if a user_id exists in the session, false otherwise.
     */
    protected function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Checks if the logged-in user has admin privileges.
     * Assumes user_id and is_admin are set in session upon login.
     *
     * @return bool True if user is logged in and is an admin, false otherwise.
     */
    protected function isAdmin(): bool {
        return $this->isLoggedIn() && ($_SESSION['is_admin'] ?? false) === true;
    }
}