<?php namespace Extensions\SelectedCharacter\Services;

use App\Models\Character\Character;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class SelectedCharacterManager extends Service
{
    /**
     * Selects a character for a user.
     *
     * @param  array                                 $data
     * @param  \App\Models\User\User                 $user
     * @return  bool
     */
    public function selectCharacter($data, $user)
    {
        DB::beginTransaction();

        try {
            // Ensure the character is present and visible to be selected,
            // and belongs to the user
            $character = Character::visible()->where('id', $data['character_id'])->first();
            if(!$character) throw new \Exception('Invalid character selected.');
            if($character->user_id != $user->id) throw new \Exception('You can\'t select a character that doesn\'t belong to you.');

            $user->settings->selected_character_id = $character->id;
            $user->settings->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
