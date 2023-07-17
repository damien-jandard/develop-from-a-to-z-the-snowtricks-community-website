<?php

namespace App\Service;

use App\Entity\Trick;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService
{
    public function __construct(
        private $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException($e);
        }
        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function uploadPictures(Trick $trick): void
    {
        foreach ($trick->getPictures() as $picture) {
            if ($picture->getFile() !== null) {
                $picture->setName($this->upload($picture->getFile()));
            }
        }
    }

    public function uploadVideos(Trick $trick): void
    {
        foreach ($trick->getVideos() as $video) {
            $host = parse_url($video->getVideoId(), PHP_URL_HOST);
            parse_str(parse_url($video->getVideoId(), PHP_URL_QUERY), $videoId);

            if ($host === 'www.youtube.com' && array_key_exists('v', $videoId)) {
                $video->setVideoId($videoId['v']);
                $video->setPlatform($host);
            }
        }
    }
}
