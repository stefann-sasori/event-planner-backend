<?php

namespace App\Nova;

use App\Models\Event as EventModel;
use App\Enum\EventStatusEnum;
use App\Nova\Actions\AddParticipant;
use App\Nova\Actions\RemoveParticipant;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Event extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static string $model = EventModel::class;

    /**
     * The single value that should be used to represent the resource.
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'name', 'location'
    ];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Textarea::make('Description')
                ->alwaysShow()
                ->hideFromIndex(),

            Text::make('Location')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Capacity')
                ->min(0)
                ->step(1)
                ->rules('required', 'integer', 'min:0')
                ->sortable(),

            Number::make('Wait List Capacity', 'waitListCapacity')
                ->min(0)
                ->step(1)
                ->rules('required', 'integer', 'min:0')
                ->sortable(),

            Badge::make('Status', 'status')
                ->map([
                    EventStatusEnum::DRAFT->value => 'warning',
                    EventStatusEnum::LIVE->value => 'success',
                ])
                ->sortable(),
            Text::make('Filling rate', function () {
                $totalParticipants = $this->participants->count();
                $capacity = $this->capacity ?? 1;
                $percentage = ($totalParticipants / $capacity) * 100;

                return number_format($percentage, 1) . '%';
            })
                ->sortable(),

            DateTime::make('Starts At', 'starts_at')
                ->rules('required', 'date')
                ->sortable(),

            DateTime::make('Ends At', 'ends_at')
                ->rules('required', 'date', 'after_or_equal:starts_at')
                ->sortable(),

            new Panel('Timestamps', $this->timestamps()),
            BelongsToMany::make('Participants', 'participants', User::class),
            BelongsToMany::make('Wait list', 'waitListUsers', User::class),
        ];
    }

    /**
     * Group fields in a nice panel for timestamps.
     */
    protected function timestamps(): array
    {
        return [
            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
        ];
    }

    /**
     * Customize which actions are available.
     */
    public function actions(NovaRequest $request): array
    {
        return [
        ];
    }

    /**
     * Show this resource in navigation.
     */
    public static function availableForNavigation(Request $request): bool
    {
        return true;
    }
}
