<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$userId= $argv[1];
$postId= $argv[2];

try {
    $ffmpeg = \FFMpeg\FFMpeg::create([
        'ffmpeg.binaries' => 'I:\Projects\ffmpeg\ffmpeg.exe',
        'ffprobe.binaries' => 'I:\Projects\ffmpeg\ffprobe.exe',
        'timeout' => 3600, // The timeout for the underlying process
        'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
    ]);
    $ffprobe = \FFMpeg\FFProbe::create([
        'ffmpeg.binaries' => getenv('FFMPEG_BINARIES'),
        'ffprobe.binaries' => getenv('FFPROBE_BINARIES'),
    ]);

    $path = 'storage/posts/' . $userId . '/' . $postId;
    $absolutePath = storage_disk('posts/' . $userId . '/' . $postId . '/');
    $relativePath = $userId . '/' . $postId;
    $dir = new \ricwein\FileSystem\Directory(new ricwein\FileSystem\Storage\Disk(dirname(__DIR__), $path));

    $post = \App\Models\Post::builder()->match('p', 'Post', ['uuid' => $postId]);
    $k = 0;
    foreach ($dir->list(true)->files() as $key => $file) {

        $k = $k + 1;
        $fileName = $file->storage()->getDetails()['path']['filename'];
        $filePath = $file->storage()->getDetails()['path']['realpath'];

        if (explode('/', $file->getType())[0] === 'video') {
            $framePath = explode('.', $fileName)[0] . '.jpg';
            $video = $ffmpeg->open($filePath);
            $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5))->save($absolutePath . $framePath);

            $duration = $ffprobe->format($filePath)->get('duration');

            $post->createConstraint('v' . $k, 'Video', [
                'duration' => $duration,
                'name' => $fileName,
                'path' => $relativePath
            ], '<-[:has]-(p)');

        } else {
            $fileWithoutExt = explode('.', $file->path()->filename)[0];
            $rawPath = $dir->path()->raw . '/' . $fileWithoutExt;
            if (is_readable($rawPath . '.mkv') || is_readable($rawPath . '.mp4')) {
                continue;
            } else {
                $post->createConstraint('p' . $k, 'Photo', [
                    'name' => $fileName,
                    'path' => $relativePath
                ], '<-[:has]-(p)');
            }
        }
    }
    $post->build()->return();

} catch (Exception $e) {
    \Core\Log::exception($e);
}