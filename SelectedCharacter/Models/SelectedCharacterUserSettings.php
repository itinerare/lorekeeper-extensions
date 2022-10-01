<?php

namespace Extensions\SelectedCharacter\Models;

use App\Models\Character\Character;
use App\Models\Model;
use App\Models\User\UserSettings;

class SelectedCharacterUserSettings extends UserSettings {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_settings';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character the user has selected if appropriate.
     */
    public function selectedCharacter() {
        return $this->belongsTo(Character::class, 'selected_character_id')->visible();
    }
}
