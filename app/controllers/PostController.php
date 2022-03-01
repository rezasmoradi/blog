<?php


namespace App\Controllers;

use App\models\Post;
use App\policies\PostPolicy;
use App\resources\PostResource;
use Core\Auth;
use Core\Controller;
use Core\Log;
use Core\Request;
use League\Flysystem\FilesystemException;
use ricwein\FileSystem\Directory;
use ricwein\FileSystem\File;
use ricwein\FileSystem\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        if (empty($request->query())) {
            $posts = Post::builder()->paginate(200)->get();
        } else {
            $posts = Post::builder()
                ->where([array_keys($request->get())[0], array_values($request->get())[0]])
                ->paginate(10)
                ->get();
        }

        response(['posts' => PostResource::collection($posts)]);
    }

    public function upload(Request $request)
    {
        $user = Auth::user();
        $post = Post::builder()
            ->match('u', 'User', ['uuid' => $user->uuid])
            ->createConstraint('p', 'Post', [], '-[rel:belongs_to]->(u)')
            ->build()
            ->return('p')[0]
            ->properties();

        $path = 'storage/posts/' . $user->uuid . '/' . $post['uuid'];

        if (!empty($request->files())) {
            try {
                $dir = new Directory(new Storage\Disk(dirname(dirname(__DIR__)), $path));
                if (!$dir->isReadable()) $dir->mkdir();
                foreach ($request->files() as $key => $file) {
                    $type = $file['type'];
                    $ext = explode('/', $type);
                    $fileName = md5(uniqid($user->uuid . time(), true));
                    if (in_array($ext[1], ['mp4', 'mkv', 'jpg', 'png', 'jpeg']) && $file['size'] <= 104857600) {
                        $uploaded = new File(new Storage\Disk\Uploaded($file));
                        $uploaded->moveTo(new Storage\Disk($dir->path()->raw . '/' . $fileName . '.' . $ext[1]));
                    } else {
                        http_response_code(422);
                        throw new \Exception('Media type ' . $type . ' is not allowed or file size is bigger than 100 MB');
                    }
                }
                response(['post' => $post], 201);
            } catch (\Exception $e) {
                Log::error($e);
                response($e->getMessage(), 500);
            } catch (FilesystemException $e) {
                response($e->getMessage(), 500);
            }
        } else {
            response('file to upload not found!', 404);
        }
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $postId = $request->post('post_id');

        if (!empty($request->post())) {
            try {
                $p = Post::builder()
                    ->match('p', 'Post', ['uuid' => $postId])
                    ->update($request->post());

                response(['post' => $p], 202);

            } catch (\Exception $exception) {
                Log::exception($exception);
                response('File not found', 404);
            }

            pclose(popen('start /B php I:\Projects\medium\core\process_video.php ' . $user->uuid . ' ' . $postId, 'r'));

        } else {
            response('No Content', 204);
        }
    }

    public function update(Request $request)
    {
        $post = Post::builder()->match('p', 'Post', ['uuid' => $request->get('id')])->update($request->post());

        response($post, 202);
    }

    public function show(Request $request)
    {
        $post = Post::builder()->where(['uuid' => $request->get()['id']])->first();
        if ($post) {
            $resource = new PostResource($post);
            response(['post' => $resource->toArray()]);
        } else {
            response('post not found', 404);
        }
    }

    public function like(Request $request)
    {
        $liked = Post::builder()
            ->match('u', 'User', ['uuid' => Auth::user()->uuid])
            ->match('p', 'Post', ['uuid' => $request->get('id')], '-[r:liked_by]->(u)')
            ->build()
            ->return('r');
        if (array_key_exists(0, $liked)) {
            response('the post has already been liked');
        } else {
            Post::builder()
                ->match('u', 'User', ['uuid' => Auth::user()->uuid])
                ->match('p', 'Post', ['uuid' => $request->get('id')])
                ->createRelation('p', '-[r:liked_by]->', 'u')
                ->build()
                ->return();
            response('the post liked successfully');
        }
    }

    public function unlike(Request $request)
    {
        $liked = Post::builder()
            ->match('p', 'Post', ['uuid' => $request->get('id')])
            ->match('u', 'User', ['uuid' => Auth::user()->uuid], '<-[r:liked_by]-(p)')
            ->build()
            ->return('r');
        if (array_key_exists(0, $liked)) {
            Post::builder()
                ->match('p', 'Post', ['uuid' => $request->get('id')])
                ->match('u', 'User', ['uuid' => Auth::user()->uuid], '<-[r:liked_by]-(p)')
                ->delete(['r']);
            response('post unliked successfully');
        } else {
            response('the post is not liked');
        }
    }

    public function delete(Request $request)
    {
        try {
            $query = Post::builder()->match('p', 'Post', ['uuid' => $request->get('id')], '-[rel:has]->(b)');
            $post = $query->build()->return(['p']);

            if ($post) {
                if (PostPolicy::delete($post[0]->properties()['uuid'])) {
                    $post = $post[0]->properties();
                    $result = $query->delete(['p', 'b']);

                    if ($result[0]['stats']['nodes-deleted'] > 0) {
                        $path = $path = 'storage/posts/' . Auth::user()->uuid . '/' . $post['uuid'];
                        $dir = new Directory(new Storage\Disk(dirname(dirname(__DIR__)), $path));
                        $dir->remove();
                        response('post deleted successfully');
                    } else {
                        response('there is no post to delete', 404);
                    }
                } else {
                    response('Unauthorized', 401);
                }
            } else {
                response('Post not found', 404);
            }
        } catch (\Exception $e) {
            Log::exception($e);
            response('Unable to delete post');
        } catch (FilesystemException $e) {
            Log::exception($e);
        }
    }
}