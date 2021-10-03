<?php

namespace App\Http\Abilities;
use Illuminate\Support\Arr;
use App\Models\ConvertRequest;
use App\Http\Abilities\FormatAbility;

trait MapDetailsAbility
{
    use FormatAbility;

    public function instagramMap(ConvertRequest $convertRequest): array
    {
        $firstThumbnail = Arr::get($convertRequest, 'details.thumbnail');
        $duration = Arr::get($convertRequest, 'details.duration');
        $durationFormatted = $this->getFormattedDuration($duration);
        $description = Arr::get($convertRequest, 'details.description');
        $description = str_replace("\n", "<br/>", $description);

        $uploadedDate = Arr::get($convertRequest, 'details.timestamp');
        if (!empty($uploadedDate)) {
            $uploadedDate = date("Y-m-d H:i:s", $uploadedDate);
        }

        return [
            'title' => Arr::get($convertRequest, 'details.title'),
            'description' => $description,
            'tags' => null,
            'thumbnail' => $firstThumbnail,
            'categories' => null,
            'like_count' => Arr::get($convertRequest, 'details.like_count'),
            'upload_date' => Arr::get($convertRequest, 'details.upload_date'),
            'duration' => $duration,
            'duration_formatted' => $durationFormatted,
        ];
    }

    public function youtubeMap(ConvertRequest $convertRequest): array
    {
        $firstThumbnail = Arr::get($convertRequest, 'details.thumbnail');
        $duration = Arr::get($convertRequest, 'details.duration');
        $durationFormatted = $this->getFormattedDuration($duration);
        $description = Arr::get($convertRequest, 'details.description');
        $description = str_replace("\n", "<br/>", $description);

        return [
            'title' => Arr::get($convertRequest, 'details.title'),
            'description' => $description,
            'tags' => Arr::get($convertRequest, 'details.tags'),
            'thumbnail' => $firstThumbnail,
            'categories' => Arr::get($convertRequest, 'details.categories'),
            'like_count' => Arr::get($convertRequest, 'details.like_count'),
            'upload_date' => Arr::get($convertRequest, 'details.upload_date'),
            'duration' => $duration,
            'duration_formatted' => $durationFormatted,
        ];
    }

    public function facebookMap(ConvertRequest $convertRequest): array
    {
        $firstThumbnail = Arr::get($convertRequest, 'details.thumbnail');
        $duration = Arr::get($convertRequest, 'details.duration');
        $durationFormatted = $this->getFormattedDuration($duration);
        $description = Arr::get($convertRequest, 'details.fulltitle');
        $description = str_replace("\n", "<br/>", $description);
        $title = Arr::get($convertRequest, 'details.title');
        $uploadedDate = Arr::get($convertRequest, 'details.timestamp');
        if (!empty($uploadedDate)) {
            $uploadedDate = date("Y-m-d H:i:s", $uploadedDate);
        }

        return [
            'title' => Arr::get($convertRequest, 'details.title'),
            'description' => $description,
            'tags' => null,
            'thumbnail' => $firstThumbnail,
            'categories' => null,
            'like_count' => null,
            'upload_date' => $uploadedDate,
            'duration' => $duration,
            'duration_formatted' => $durationFormatted,
        ];
    }
}
