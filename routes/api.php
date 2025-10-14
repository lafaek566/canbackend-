<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['cors']], function () {

    // ========================================================================
    // LOCAL API ENDPOINTS (Synced from Production)
    // These endpoints serve data from local database (carnew)
    // Use http://127.0.0.1:8000/api instead of production API
    // ========================================================================
    
    // Health check
    Route::get('health', 'Api\LocalApiController@healthCheck');
    
    // HOME PAGE
    Route::get('top-three-judges', 'Api\LocalApiController@topThreeJudges');
    Route::get('top-three-participants', 'Api\LocalApiController@topThreeParticipants');
    
    // NEWS
    Route::get('news-list-all', 'Api\LocalApiController@newsListAll');
    Route::get('news-list/all', 'Api\LocalApiController@newsListAll'); // Alias
    Route::get('news-detail/{id}', 'Api\LocalApiController@newsDetail');
    
    // SPONSORS
    Route::get('news-list-sponsor-tier', 'Api\LocalApiController@newsListSponsorTier');
    Route::get('news-list-sponsor-all', 'Api\LocalApiController@newsListSponsorAll');
    Route::get('news-list-sponsor-id/{id}', 'Api\LocalApiController@newsListSponsorId');
    Route::get('country-sponsor-list/all', 'Api\LocalApiController@countrySponsorListAll');
    
    // EVENTS (Local - override existing if needed)
    Route::get('event-upcoming/list', 'Api\LocalApiController@eventUpcomingList');
    Route::get('event-past/list', 'Api\LocalApiController@eventPastList');
    Route::get('event-detail/{id}', 'Api\LocalApiController@eventDetail');
    
    // USERS
    Route::get('user-list/all', 'Api\LocalApiController@userListAll');
    Route::get('user-detail/{id}', 'Api\LocalApiController@userDetail');
    Route::get('judge-list-all', 'Api\LocalApiController@judgeListAll');
    
    // CARS
    Route::get('car-list/all', 'Api\LocalApiController@carListAll');
    Route::get('car-detail/{id}', 'Api\LocalApiController@carDetail');
    
    // ========================================================================
    // END LOCAL API ENDPOINTS
    // ========================================================================

    // Route::post('confirmation-code', 'Auth\ApiRegisterController@confirmCode');
    Route::post('register', 'Auth\ApiRegisterController@register');
    Route::post('resend-code', 'Auth\ApiRegisterController@resendConfirmCode');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->middleware('auth:api');

    Route::post('sent-reset-password-link', 'Auth\ApiRegisterController@sentResetPassword');
    Route::post('reset-password', 'Auth\ApiRegisterController@resetPassword');
    Route::post('check-reset-password-link', 'Auth\ApiRegisterController@checkResetPasswordLink');
    // Route::post('register-invite', 'Auth\ApiRegisterController@registerByInvites');

    Route::get('audio-listen/{public}/{audio}/{user_id}/{audio_id}', 'AudioController@listenAudio');

    //users
    Route::get('user-list/top-sixteen', 'UserController@listAllTopSixteen');
    Route::get('user-list/top-sixteen-leaderboard', 'UserController@listAllTopSixteenLeaderboardMenu');
    Route::post('user-filter-leaderboard', 'UserController@filterLeaderboard');

    //user profiles
    Route::get('user-profile-detail/{user_id}', 'UserProfileController@listDetail');

    //Events (OLD - COMMENTED, USE LocalApiController ABOVE)
    // Route::get('event-upcoming/list', 'EventController@listAndCountUpcomingLimit');
    // Route::get('event-upcoming/list/order', 'EventController@listUpcomingOrder');
    // Route::get('event-past/list', 'EventController@listAndCountPastLimit');
    // Route::get('event-past/list/order', 'EventController@listPastOrder');
    Route::get('event-association-upcoming/list', 'EventController@listAndCountUpcomingLimitAssociation');
    Route::get('event-association-upcoming/list/order', 'EventController@listUpcomingOrderAssociation');
    Route::get('event-association-past/list', 'EventController@listAndCountPastLimitAssociation');
    Route::get('event-association-past/list/order', 'EventController@listPastOrderAssociation');
    // Route::get('event-detail/{id}', 'EventController@listDetail'); // USE LocalApiController
    Route::get('event-participated-cars/{id}', 'EventController@listParticipatedCars');
    Route::get('event-sponsor/list/{sponsor_id}', 'EventController@listAllBySponsorId');
    Route::get('event-judges/list/{judge_id}', 'EventController@listAllByJudgeId');
    Route::get('event-assign-judge/list', 'EventController@listAssignedEventToJudge');
    Route::get('event-activity-class-form/list/{id}', 'EventController@listActivityClassForm');
    Route::get('event-years', 'EventController@getEventYears');


    //Event Zone
    Route::get('event-zone', 'EventZoneController@getEventZone');
    Route::get('event-zone-with-trashed', 'EventZoneController@getEventZoneWithTrashed');
    Route::get('event-zone/{eventZone}', 'EventZoneController@getEventZoneWithId');
    Route::post('event-zone-store', 'EventZoneController@addEventZone');
    Route::patch('event-zone-update/{id}', 'EventZoneController@updateEventZone');
    Route::delete('event-zone-delete/{id}', 'EventZoneController@deleteEventZone');
    Route::post('event-zone-restore/', 'EventZoneController@restoreEventZone');
    Route::post('event-zone-with-country-id/{id}', 'EventZoneController@getEventZoneWithCountryId');

    //Event Zone
    Route::get('custom-event-tag', 'CustomEventTagController@getCustomEventTags');
    Route::get('custom-event-tag-with-trashed', 'CustomEventTagController@getCustomEventTagWithTrashed');
    Route::get('custom-event-tag/{customEventTag}', 'CustomEventTagController@getCustomEventTagWithId');
    Route::post('custom-event-tag-store', 'CustomEventTagController@addCustomEventTag');
    Route::patch('custom-event-tag-update/{id}', 'CustomEventTagController@updateCustomEventTag');
    Route::delete('custom-event-tag-delete/{id}', 'CustomEventTagController@deleteCustomEventTag');
    Route::post('custom-event-tag-restore/', 'CustomEventTagController@restoreCustomEventTag');
    Route::get('custom-event-tag-year/{tagYear}', 'CustomEventTagController@getCustomEventTagWithYear');



    // Country Sponsors
    Route::get('country-sponsor-list/all', 'CountrySponsorController@listAll');
    Route::get('country-sponsor-list/global', 'CountrySponsorController@listAllGlobal');
    Route::get('country-sponsor-list/local/{country_id}', 'CountrySponsorController@listAllLocal');
    Route::get('country-sponsor-list/country/{country_id}', 'CountrySponsorController@listAllByCountryId');

    // Associations Sponsors
    Route::get('association-sponsor-list/all', 'AssociationSponsorController@listAll');
    Route::get('association-sponsor-list/{association}', 'AssociationSponsorController@listAllByDetail');

    //Event judges
    Route::get('event-judge-list/{event_id}/event', 'EventJudgeController@listJudgesOfEventLimit');
    Route::get('event-judge-list/{event_id}/event/order', 'EventJudgeController@listJudgesOfEventLimitOrder');
    Route::get('event-judge-list/all', 'EventJudgeController@listAllJudgesLimit');
    Route::get('event-judge-list/all/order', 'EventJudgeController@listAllJudgesLimitOrder');

    // EVENT-JUDGE-RESULTS
    Route::get('top-three-judges', 'EventJudgeResultController@listTopThreeJudges');
    Route::get('rating-from-participant', 'EventJudgeResultController@ratingsFromParticipant');

    // Event members
    Route::get('event-member-list/{event_id}/event', 'EventMemberController@listMembersOfEventLimit');
    Route::get('event-member-list/{event_id}/event/order', 'EventMemberController@listMembersOfEventLimitOrder');
    Route::get('event-member-list/all', 'EventMemberController@listAllMembersLimit');
    Route::get('event-member-list/all/order', 'EventMemberController@listAllMembersLimitOrder');

    // EVENT-MEMBER-RESULTS
    Route::get('top-three-participants', 'EventMemberResultController@listTopThreeParticipants');
    Route::get('rating-from-judge', 'EventMemberResultController@ratingsFromJudge');

    // Winners showcase
    Route::get('winner-showcase2', 'EventController@listWinnerShowcase2');
    Route::get('winner-association-showcase2', 'EventController@listWinnerAssociationShowcase2');

    // News
    Route::get('news-list', 'NewsController@listAllByCountryId');
    Route::get('associations-news-list', 'NewsController@listAllByAssociationId');
    Route::get('news-list-sponsor-id/{user_id}', 'NewsController@listAllBySponsorId');
    // Route::get('news-list-sponsor-tier', 'NewsController@listAllBySponsorTier'); // REPLACED by Api\LocalApiController
    Route::get('news-association-list-sponsor', 'NewsController@listAllByAssociationSponsor');
    Route::post('news-list-sponsor', 'NewsController@listAllBySponsor');

    //Form Generators
    Route::get('form-generator-status-list', 'FormGeneratorController@listAllFormGeneratorByStatus');
    Route::get('form-generator-detail/{form_id}', 'FormGeneratorController@detailFormGenerator');

    //Countries
    Route::get('country-list', 'CountryController@listAll');
    Route::get('available-country-list', 'CountryController@listAllAvailable');

    //Associations
    Route::get('association-list', 'AssociationController@listAll');
    Route::get('association-list-with-trashed', 'AssociationController@listAllWithTrashed');
    Route::get('association/{association}', 'AssociationController@detail');
    Route::post('association-store', 'AssociationController@store');
    Route::post('association-restore', 'AssociationController@restore');
    Route::patch('association-update/{association}', 'AssociationController@update');
    Route::delete('association-delete/{association}', 'AssociationController@delete');

    //News
    Route::get('news-detail/{id}', 'NewsController@listDetail');


    // COMPETITION-ACTIVITIES
    Route::get('competition-activity/list', 'CompetitionActivityController@listAll');

    // COMPETITIONS
    Route::get('competition-list', 'CompetitionController@listAll');

    // CLASS-GRADES
    Route::get('class-grade/list', 'ClassGradeController@listAll');
    Route::get('class-grade/{competitionActivity}', 'ClassGradeController@getByCompetitionActivity');

    // CLASS-COUNTRIES
    Route::get('class-country/list', 'ClassCountryController@listAll');

    // SPONSORS
    Route::get('sponsor-types', 'SponsorTypeController@listAll');
    Route::get('sponsor-tiers', 'SponsorTierController@listAll');

    // CLASS-CATEGORIES
    Route::get('class-category', 'ClassCategoryController@listAllByCompetitionActivityId');

    // CLASS-GROUPS
    Route::get('class-group', 'ClassGroupController@listAllByGradeCountryCategory');

    //cars
    Route::get('car-list/all', 'CarController@listAllLimit');
    Route::get('car-list/all/order', 'CarController@listAllLimitOrder');
    Route::get('car-detail/{id}', 'CarController@listDetailByCarId');
    Route::get('car-list/{user_id}/car', 'CarController@listAllByIdUserLimit');
    Route::get('car-list/{user_id}/car/order', 'CarController@listAllByIdUserLimitOrder');
    Route::get('car-judge/list', 'CarController@listJudgedCarByJudgeId');
    Route::get('car-timeline/{user_id}/{car_id}', 'CarController@listTimeline');
    Route::get('car-timeline/{user_id}/{car_id}/order', 'CarController@listTimelineOrder');
    Route::get('car-participated-events/{user_id}/{car_id}', 'CarController@listParticipatedEvents');
    Route::get('car-participated-events/{user_id}/{car_id}/order', 'CarController@listParticipatedEventsOrder');

    Route::get('assignment-judge-participant/list', 'EventJudgeMemberAssignmentController@listJudgesAssignedToParticipant');

    // RULES
    Route::get('rules-list', 'RulesController@show');
});


Route::group(['middleware' => ['cors', 'auth:api']], function () {
    // Route::post('first-load', 'FirstLoadController@load');

    // USER
    Route::post('logout', 'Auth\LoginController@logout');
    Route::get('check-confirm-status', 'Auth\ApiRegisterController@checkConfirmStatus');
    Route::get('check-banned-status', 'Auth\ApiRegisterController@checkBannedStatus');
    Route::post('verify-email', 'Auth\ApiRegisterController@verifyEmail');
    Route::post('verify-sms-code', 'Auth\ApiRegisterController@verifySMSCode');
    Route::post('send-sms-code', 'Auth\ApiRegisterController@sendSMSCode');
    Route::post('change-email', 'Auth\ApiRegisterController@changeEmail');
    Route::post('change-email-verify', 'Auth\ApiRegisterController@changeEmailVerify');
    Route::post('change-role', 'Auth\ApiRegisterController@changeRole');
    Route::post('delete/{id}', 'Auth\ApiRegisterController@delete');
    Route::post('user-banned', 'UserController@userBanned');
    // Route::get('user-list/all', 'UserController@listAll'); // Commented: Use LocalApiController instead
    Route::get('user-list/verify', 'UserController@listVerify');
    Route::get('user-list/sponsor-admin', 'UserController@listAllSponsorAdmin');
    Route::get('user-list/all/sponsors', 'UserController@listAllSponsors');
    Route::get('users-grouped/{id}', 'UserController@listGrouped');
    Route::get('users-ungrouped', 'UserController@listUngrouped');
    Route::post('user-link', 'UserController@userLink');
    Route::post('user-unlink', 'UserController@userUnlink');
    Route::post('switch-user', 'UserController@switchUser');
    Route::post('verify/{user}', 'UserController@userVerify');
    Route::post('user-include-to-leaderboard', 'UserController@includeToLeaderBoard');
    Route::post('user-force-verify-number', 'UserController@forceVerifyNumber');

    // GALLERIES
    Route::post('gallery-store', 'GalleryController@storeUploadedPhoto');
    Route::get('gallery-list/{user_id}', 'GalleryController@listAllByUser');
    Route::post('gallery-delete', 'GalleryController@deletePhotos');

    // AUDIO
    Route::post('audio-store', 'AudioController@storeUploadedAudio');
    Route::get('audio-list/{user_id}', 'AudioController@listAllByUser');
    Route::post('audio-delete/{audio_id}', 'AudioController@deleteAudio');

    // RULES
    Route::post('rules-store', 'RulesController@store');
    Route::post('rules-update', 'RulesController@update');
    Route::post('rules-delete/{rule_id}', 'RulesController@delete');

    //FORM-GENERATOR
    Route::get('form-generator-list/{user_id}', 'FormGeneratorController@listAllFormGenerator');
    // Route::get('form-generator-detail//{form_id}', 'FormGeneratorController@detailFormGeneratorUser');
    Route::post('form-generator-store', 'FormGeneratorController@storeFormGenerator');
    Route::post('form-generator-update', 'FormGeneratorController@updateFormGenerator');
    Route::post('form-generator-status/{formGenerator}', 'FormGeneratorController@updateStatusFormGenerator');
    Route::post('form-generator-delete/{form_id}', 'FormGeneratorController@deleteFormGenerator');

    // USER-PROFILES
    Route::post('user-profile-update-avatar', 'UserProfileController@updateAvatar');
    Route::post('user-profile-update-banner', 'UserProfileController@updateBanner');
    Route::post('user-profile-update-biography', 'UserProfileController@updateBiography');
    Route::post('user-profile-update-name', 'UserProfileController@updateUsername');
    Route::post('user-profile-update-phone', 'UserProfileController@updatePhoneNo');
    Route::get('user-profile-stats/{user_id}', 'UserProfileController@listDetailStats');

    // EVENT-TYPES
    Route::get('event-type/list', 'EventTypeController@listAllEventType');

    // EVENTS
    Route::post('event-store', 'EventController@store');
    Route::post('event-finalize/{id}', 'EventController@finalizeAssessmentOfEvent');
    Route::post('event-unfinalize/{event}', 'EventController@unfinalizeAssessmentOfEvent');
    Route::patch('event-update/{id}', 'EventController@update');
    Route::post('event-delete/{id}', 'EventController@delete');
    Route::post('event-update-recap', 'EventController@updateRecap');
    Route::get('event-vp-multiplier', 'EventController@getEventVPMultiplier');

    // EVENT ZONE


    // EVENT TAG GROUP

    // EVENT-JUDGES
    Route::post('event-judge-store', 'EventJudgeController@store');
    Route::post('event-judge-add-activity', 'EventJudgeController@addActivity');
    Route::post('event-judge-store-manual', 'EventJudgeController@storeManual');
    Route::patch('event-judge-update/{id}', 'EventJudgeController@update');
    Route::patch('event-judge-update-manual/{id}', 'EventJudgeController@updateJudgeManual');
    Route::post('event-judge-delete/{id}', 'EventJudgeController@delete');
    Route::get('event-judge-list-to-rate', 'EventJudgeController@listJudgesEventToRate');
    Route::get('event-judge-detail', 'EventJudgeController@listDetail');
    Route::get('judges-available-list/{event_id}', 'EventJudgeController@listAllAvailableJudgesLimitByEventId');
    Route::get('judges-available-list-manual/{event_id}', 'EventJudgeController@listAllAvailableJudgesManualLimitByEventId');

    // EVENT-JUDGE-ACTIVITIES
    Route::get('event-judge-activity/assign/judge/{event_id}', 'EventJudgeActivityController@getActivityAssignedToJudge');

    // EVENT-JUDGE-RESULTS
    Route::post('event-judge-result-store', 'EventJudgeResultController@store');
    Route::get('event-judge-rated-list', 'EventJudgeResultController@listEventJudgesRated');

    // CARS
    Route::post('car-store', 'CarController@store');
    Route::patch('car-update/{id}', 'CarController@update');
    Route::post('car-delete/{id}', 'CarController@delete');

    // EVENT-MEMBERS
    Route::post('event-member-store', 'EventMemberController@store');
    Route::post('event-member-store-manual', 'EventMemberController@storeManual');
    Route::post('event-member-store-car-manual', 'EventMemberController@storeCarManual');
    Route::patch('event-member-update/{event_member_id}', 'EventMemberController@update');
    Route::patch('event-member-update-manual/{event_member_id}', 'EventMemberController@updateMemberManual');
    Route::post('event-member-update-score', 'EventMemberController@updateScore');
    Route::post('event-member-update-car', 'EventMemberController@updateCar');
    Route::post('event-member-delete/{event_member_id}', 'EventMemberController@delete');
    Route::get('event-member-list/all/member/{user_id}', 'EventMemberController@listAllMembersLimitByUserId');
    Route::get('event-member-detail', 'EventMemberController@listDetail');
    Route::get('event-member-list-to-rate', 'EventMemberController@listMembersEventToRate');
    Route::get('members-available-list/{event_id}', 'EventMemberController@listAllAvailableParticipantsLimitByEventId');
    Route::get('members-available-list-manual/{event_id}', 'EventMemberController@listAllAvailableParticipantsManualLimitByEventId');
    Route::get('can-q-point-participant/list/all', 'EventMemberController@listCanQPointAllParticipants');

    // EVENT-MEMBER-CLASSES
    Route::post('disqualify-participant-class/{event_member_class_id}', 'EventMemberClassController@disqualifyParticipantClass');
    Route::post('event-member-class-delete/{event_member_class_id}', 'EventMemberClassController@deleteParticipantListClass');
    Route::post('event-member-class-finalize/{event_member_class_id}', 'EventMemberClassController@finalize');
    Route::post('event-member-class-mass-finalize', 'EventMemberClassController@massFinalize');
    Route::post('event-member-class-unfinalize/{eventMemberClass}', 'EventMemberClassController@unfinalize');
    Route::get('check-disqualified-status-participant/{event_member_class_id}', 'EventMemberClassController@getParticipantDisqualifiedStatus');
    Route::get('can-q-point-participant/list', 'EventMemberClassController@getParticipantPointEachEventMemberClass');
    Route::get('event-member/{classGroup}', 'EventMemberClassController@getParticipantFromClassGroup');
    Route::post('event-member-assessment-store/{event_member_class_id}', 'EventMemberClassController@storeAssessment');
    Route::post('event-member-assessment-reset/{eventMemberClass}', 'EventMemberClassController@resetAssessment');
    Route::post('event-member-assessment-update/{event_member_class_id}', 'EventMemberClassController@updateAssessment');
    Route::post('event-member-assessment-random/{event_member_class_id}', 'EventMemberClassController@randomAssessment');

    // EVENT-MEMBER-RESULTS
    Route::post('event-member-result-store', 'EventMemberResultController@store');
    Route::get('event-member-rated-list', 'EventMemberResultController@listEventMembersRated');

    // EVENT-JUDGE-MEMBER-ASSIGNMENTS
    Route::post('assignment-store', 'EventJudgeMemberAssignmentController@store');
    Route::post('assignment-delete', 'EventJudgeMemberAssignmentController@delete');
    Route::get('assignment-participant-available/list', 'EventJudgeMemberAssignmentController@listParticipantsAvailable');
    Route::get('assignment-participant-all-available/list', 'EventJudgeMemberAssignmentController@listParticipantsAllAvailable');
    Route::get('assignment-participant-judge/list', 'EventJudgeMemberAssignmentController@listParticipantsAssignedToJudge');
    Route::get('assignment-participant-judge-per-activity/list/incompleted', 'EventJudgeMemberAssignmentController@listParticipantsAssignedToJudgePerActivityIncomplete');
    Route::get('assignment-participant-judge-per-activity/list/completed', 'EventJudgeMemberAssignmentController@listParticipantsAssignedToJudgePerActivityComplete');
    Route::get('assignment-participant-judge-per-activity/list/skipped', 'EventJudgeMemberAssignmentController@listParticipantsAssignedToJudgePerActivitySkipped');

    // COUNTRIES
    Route::post('country-store', 'CountryController@store');
    Route::patch('country-update/{id}', 'CountryController@update');
    Route::post('country-delete/{id}', 'CountryController@delete');

    // COUNTRY-SPONSORS
    Route::post('country-sponsor-store', 'CountrySponsorController@store');
    // Route::get('country-sponsor-country-id/{user_id}', 'CountrySponsorController@getCountryId');
    Route::post('country-sponsor-delete/{id}', 'CountrySponsorController@delete');

    // ASSOCIATION-SPONSORS
    Route::post('association-sponsor-store', 'AssociationSponsorController@store');
    Route::post('association-sponsor-delete/{id}', 'AssociationSponsorController@delete');

    // NEWS
    Route::post('news-store', 'NewsController@store');
    Route::patch('news-update/{id}', 'NewsController@update');
    Route::get('news-list/all', 'NewsController@listAll');
    Route::get('news-list-sponsor-admin', 'NewsController@listAllBySponsorAdmin');
    Route::post('news-delete/{id}', 'NewsController@delete');

    // COMPETITION-ACTIVITIES
    Route::get('classes/all', 'CompetitionActivityController@listAllClasses');
    Route::get('classes/{competition_activity_id}', 'CompetitionActivityController@listAllClassesPerActivity');

    // CLASS-GROUPS
    Route::post('class-group-store', 'ClassGroupController@store');
    Route::post('class-group-disabled', 'ClassGroupController@disabled');

    ########--------------------- ASSESSMENTS

    // TIME
    Route::get('time-stamp', 'TimeController@getTimeStamp');

    // DANCE-SUB-ASSESSMENTS
    Route::get('dance-sub-assessment/list/{dance_major_aspect_id}', 'DanceSubAssessmentController@getDanceSubAssessmentByDanceMajorAspectId');

    // CAN-Q
    Route::post('can-q-store', 'CanQScoreController@store');
    Route::post('can-q-imaging-position-store', 'CanQScoreController@storeImagingPositionAndFocus');
    Route::post('can-q-listening-pleasure-store', 'CanQScoreController@storeListeningPleasure');
    Route::post('can-q-spectral-balance-store', 'CanQScoreController@storeSpectralBalance');
    Route::post('can-q-staging-store', 'CanQScoreController@storeStaging');
    Route::post('can-q-tonal-accuracy-store', 'CanQScoreController@storeTonalAccuracy');
    Route::patch('can-q-update', 'CanQScoreController@update');
    Route::patch('can-q-imaging-position-update', 'CanQScoreController@updateImagingPosition');
    Route::patch('can-q-listening-pleasure-update', 'CanQScoreController@updateListeningPleasure');
    Route::patch('can-q-spectral-balance-update', 'CanQScoreController@updateSpectralBalance');
    Route::patch('can-q-staging-update', 'CanQScoreController@updateStaging');
    Route::patch('can-q-tonal-accuracy-update', 'CanQScoreController@updateTonalAccuracy');
    Route::get('can-q-list-participant/judge/complete', 'CanQScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-q-list-participant/judge/incomplete', 'CanQScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-q-list-participant/all/complete', 'CanQScoreController@listAllParticipantAssessed');
    Route::get('can-q-list-participant/all/incomplete', 'CanQScoreController@listAllParticipantNotAssessed');

    // CAN-LOUD
    Route::post('can-loud-store', 'CanLoudScoreController@store');
    Route::patch('can-loud-update/{id}', 'CanLoudScoreController@update');
    Route::get('can-loud-list-participant/judge/complete', 'CanLoudScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-loud-list-participant/judge/incomplete', 'CanLoudScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-loud-list-participant/all/complete', 'CanLoudScoreController@listAllParticipantAssessed');
    Route::get('can-loud-list-participant/all/incomplete', 'CanLoudScoreController@listAllParticipantNotAssessed');

    // CAN-JAM
    Route::post('can-jam-store', 'CanJamScoreController@store');
    Route::patch('can-jam-update/{id}', 'CanJamScoreController@update');
    Route::get('can-jam-max-score/{event_member_class_id}', 'CanJamScoreController@getMaxScore');
    Route::get('can-jam-list-participant/judge/complete', 'CanJamScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-jam-list-participant/judge/incomplete', 'CanJamScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-jam-list-participant/all/complete', 'CanJamScoreController@listAllParticipantAssessed');
    Route::get('can-jam-list-participant/all/incomplete', 'CanJamScoreController@listAllParticipantNotAssessed');


    // CAN-CRAFT
    Route::get('can-craft-pro-extreme/list/{event_member_class_id}', 'CanCraftScoreController@listProExtreme');
    Route::post('can-craft-store', 'CanCraftScoreController@store');
    Route::patch('can-craft-update/{id}', 'CanCraftScoreController@update');
    Route::get('can-craft-list-participant/judge/complete', 'CanCraftScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-craft-list-participant/judge/incomplete', 'CanCraftScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-craft-list-participant/all/complete', 'CanCraftScoreController@listAllParticipantAssessed');
    Route::get('can-craft-list-participant/all/incomplete', 'CanCraftScoreController@listAllParticipantNotAssessed');

    // CAN-TUNE
    Route::get('can-tune-consumer-winner/list/{event_id}', 'CanTuneScoreController@getConsumerWinner');
    Route::get('can-tune-status-consumer-pyramid/{event_id}', 'CanTuneScoreController@checkConsumerPyramid');
    Route::get('can-tune-status-prosumer-pyramid/{event_id}', 'CanTuneScoreController@checkProsumerPyramid');
    Route::get('can-tune-status-professional-pyramid/{event_id}', 'CanTuneScoreController@checkProfessionalPyramid');
    Route::get('can-tune-bracket/list/{class_grade_id}', 'CanTuneScoreController@getBracket');
    Route::get('can-tune-judges/list/{event_id}', 'CanTuneScoreController@getJudgeCanTuneEvent');
    Route::get('can-tune-participants/list/{event_id}', 'CanTuneScoreController@getParticipantRegistered');
    Route::post('can-tune-store-consumer', 'CanTuneScoreController@storeConsumer');
    Route::patch('can-tune-update-consumer/{id}', 'CanTuneScoreController@updateConsumer');
    Route::post('can-tune-store-prosumer', 'CanTuneScoreController@storeProsumer');
    Route::patch('can-tune-update-prosumer/{id}', 'CanTuneScoreController@updateProsumer');
    Route::post('can-tune-store-professional', 'CanTuneScoreController@storeProfessional');
    Route::patch('can-tune-update-professional/{id}', 'CanTuneScoreController@updateProfessional');
    Route::get('can-tune-consumer/list', 'CanTuneScoreController@getConsumerList');
    Route::post('can-tune-consumer/submit/prosumer/{event_id}', 'CanTuneScoreController@consumerSubmitProsumer');
    Route::get('can-tune-prosumer/list', 'CanTuneScoreController@getProsumerList');
    Route::post('can-tune-prosumer/submit/professional/{event_id}', 'CanTuneScoreController@prosumerSubmitProfessional');
    Route::get('can-tune-prosumer-winner/list/{event_id}', 'CanTuneScoreController@getProsumerWinner');
    Route::get('can-tune-professional/list', 'CanTuneScoreController@getProfessionalList');
    Route::post('can-tune-professional/submit/final/{event_id}', 'CanTuneScoreController@professionalSubmitFinal');
    Route::get('can-tune-professional-winner/list/{event_id}', 'CanTuneScoreController@getProfessionalWinner');

    // CAN-PERFORM
    Route::post('can-perform-store', 'CanPerformScoreController@store');
    Route::patch('can-perform-update', 'CanPerformScoreController@update');
    Route::get('can-perform-list-participant/judge/complete', 'CanPerformScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-perform-list-participant/judge/incomplete', 'CanPerformScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-perform-list-participant/all/complete', 'CanPerformScoreController@listAllParticipantAssessed');
    Route::get('can-perform-list-participant/all/incomplete', 'CanPerformScoreController@listAllParticipantNotAssessed');

    // CAN-DANCE
    Route::post('can-dance-store', 'CanDanceScoreController@store');
    Route::patch('can-dance-update', 'CanDanceScoreController@update');
    Route::get('can-dance-list-participant/judge/complete', 'CanDanceScoreController@listParticipantOfJudgeAssessed');
    Route::get('can-dance-list-participant/judge/incomplete', 'CanDanceScoreController@listParticipantOfJudgeNotAssessed');
    Route::get('can-dance-list-participant/all/complete', 'CanDanceScoreController@listAllParticipantAssessed');
    Route::get('can-dance-list-participant/all/incomplete', 'CanDanceScoreController@listAllParticipantNotAssessed');

    // WINNER-SHOWCASE
    Route::get('winner-showcase', 'EventController@listWinnerShowcase');
    Route::get('winner-showcase-global', 'EventController@listWinnerShowcaseGlobal');

    // EVENT ACTIVITY FORMS // not used
    Route::post('activity-form-store', 'EventActivityFormController@store');
    Route::get('activity-form/list', 'EventActivityFormController@listActivityFormsAvailable');

    // EVENT ACTIVITY CLASS FORMS
    Route::get('activity-form-list/{user_id}', 'EventActivityClassFormController@listActivityFormsAvailable');
    Route::post('activity-form-assign', 'EventActivityClassFormController@assignFormId');
    Route::post('activity-form-delete', 'EventActivityClassFormController@deleteAssignFormId');
});
