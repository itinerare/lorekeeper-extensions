<?php

namespace Extensions\SelectedCharacter\Http\Controllers;

use Extensions\SelectedCharacter\Services\SelectedCharacterManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SelectedCharacterController extends Controller {
    /**
     * Sets the user's selected character.
     *
     * @param App\Services\CharacterManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSelectCharacter(Request $request, SelectedCharacterManager $service) {
        if ($service->selectCharacter($request->only(['character_id']), Auth::user())) {
            flash('Character selected successfully.')->success();

            return redirect()->back();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
