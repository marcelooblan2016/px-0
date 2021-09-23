<?php

namespace App\Http\Abilities;
use Illuminate\Support\Arr;
use App\Models\ConvertRequest;
use App\Http\Abilities\FormatAbility;

trait MapDetailsAbility
{
    use FormatAbility;

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
}
