<?php
    declare(strict_types = 1);

        require_once '../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->safeLoad();
    
    require_once '../system/db.php';
    require_once '../system/logger.php';
	require_once '../system/flysystem.php';
    require_once '../system/functions.php';
    require_once '../classes/class-account.php';
    require_once '../classes/class-company.php';

    use League\Flysystem\FilesystemException;
    use League\Flysystem\UnableToReadFile;
    
    $account = new Account($_SESSION['user_id']);
    $company = new Company($account->get_id());    
    
    if (isset($_SERVER['HTTP_X_AVATAR_UPLOAD'])) {
        $id = $account->get_id();
        if (upload_file('file-0', "\\avatars\\$id\\", ) == true) {
            $log->info("Avatar upload finished");
        } else {
            $log->info("Avatar upload failed");
        }        
    }
    
    function upload_file($input_id, $output_directory, $filename = null) {
        global $log, $filesystem, $account;
        
        $allowed_mimes = [
            'jpg'  => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];

        if (!isset($_FILES[$input_id])) {
            $log->error("File with input ID $input_id has not been uploaded: No file provided.");
        }

        $mime_type     = mime_content_type($_FILES[$input_id]["tmp_name"]);        
        $temp_dir      = "../uploads/temp";
        $temp_filename = basename($_FILES[$input_id]["name"]);
        $temp_file     = "$temp_dir/$temp_filename";
        
        $temp_type     = explode('.', $_FILES[$input_id]['name']);
        $image_type    = end($temp_type);

        $final_file = "";
        if ($filename == null) {
            $final_file    = "player_avatar.$image_type";
        }

        $check_size    = $_FILES[$input_id]['size'];
        $upload_good   = 1;

        if (!file_exists($output_directory)) {
            $log->info("Output directory does not exist: $output_directory - creating");
            mkdir($output_directory, 0755, true);
        }

        if (file_exists($final_file)) {
            $log->error("File already exists: $final_file");
            $upload_good = 'File already exists';
        }

        if ($check_size >= 10000000) {
            $log->error("File too large: $check_size");
            $upload_good = "File is too large";
        }

        // Checks MIME, then extension
        if (!isset($allowed_mimes[$image_type]) || !array_search($mime_type, $allowed_mimes)) {
            $log->error("Wrong MIME/extension",  ['ext' => $image_type, 'mime' => $mime_type]);
            $upload_good = "Wrong extension or MIME";
        }

        if ($upload_good == 1) {
            if (move_uploaded_file($_FILES[$input_id]["tmp_name"], $temp_file) == false) {
                $log->error("Failed to move temp file $temp_file");
            } else {
                $log->info("Moved temp file $temp_file to $final_file");
            }
        } else {
            echo "Invalid upload: $upload_good";
            return -1;
        }

        $response = null;

        try {
            $response = $filesystem->readStream('\temp\\' . $temp_filename);
        } catch (FilesystemException | UnableToReadFile $exception) {
            $log->error($exception->getMessage());
        }

        try {
            $filesystem->writeStream("$output_directory\\$final_file", $response);
        } catch (FilesystemException | UnableToReadFile $exception) {
            $log->error($exception->getMessage());
        }

        $account->set_avatar("/uploads/avatars/" . $account->get_id() . "/$final_file");
    }