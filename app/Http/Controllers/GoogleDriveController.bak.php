<!-- <?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;

class GoogleDriveController extends Controller
{
    private $googleClient;

    public function __construct()
    {
        $this->googleClient = new Client();
        $this->googleClient->setClientId(config('google.client_id'));
        $this->googleClient->setClientSecret(config('google.client_secret'));
        $this->googleClient->setRedirectUri(config('google.redirect'));
        $this->googleClient->addScope(Drive::DRIVE);
    }

    /**
     * Redirect to Google OAuth authentication page.
     */
    public function redirectToGoogle()
    {
        $authUrl = $this->googleClient->createAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Handle the callback from Google OAuth.
     */
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('home')->with('error', 'Failed to get Google authorization code.');
        }

        // Exchange the authorization code for an access token
        $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);

        // Store the access token in session
        session(['google_access_token' => $token]);

        // Redirect to a page where you can make further API calls
        return redirect()->route('google.drive.files');
    }

    /**
     * List files from Google Drive.
     */
    public function listFiles()
    {
        $token = session('google_access_token');
        if (!$token) {
            return redirect()->route('google.login');
        }

        $this->googleClient->setAccessToken($token);
        if ($this->googleClient->isAccessTokenExpired()) {
            return redirect()->route('google.login');
        }

        // Create the Drive service object
        $driveService = new Drive($this->googleClient);
        $files = $driveService->files->listFiles();

        return view('google.files', compact('files'));
    }

    /**
     * Upload file to Google Drive.
     */
    public function uploadFile(Request $request)
    {
        $token = session('google_access_token');
        if (!$token) {
            return redirect()->route('google.login');
        }

        $this->googleClient->setAccessToken($token);
        if ($this->googleClient->isAccessTokenExpired()) {
            return redirect()->route('google.login');
        }

        // Handle file upload
        $file = $request->file('file');
        if (!$file) {
            return back()->with('error', 'No file selected.');
        }

        // Create Drive service
        $driveService = new Drive($this->googleClient);

        // Create file metadata
        $fileMetadata = new Drive\DriveFile([
            'name' => $file->getClientOriginalName(),
        ]);

        // Upload file
        $content = file_get_contents($file->getRealPath());
        $driveFile = $driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        return redirect()->route('google.drive.files')->with('success', 'File uploaded successfully!');
    }
} -->
