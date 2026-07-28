<?php

namespace App\Sys\Build;

use App\Actions\Fortify\CreateNewUser;
use App\Data\ApiParams\Data\Attributes\Params\AttributeParamData;
use App\Data\ApiParams\Data\Namespaces\Params\NamespaceParamData;
use App\Data\ApiParams\Data\Types\Params\TypeParamData;
use App\Helpers\NamespacePresetUuids;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\ElementType;
use App\Models\ElementTypeParent;
use App\Models\Phase;
use App\Models\Server;
use App\Models\User;
use App\Models\UserNamespace;
use App\Sys\Res\Atr\INewSystemAttribute;
use App\Sys\Res\IDocument;
use App\Sys\Res\Namespaces\SystemNamespace;
use App\Sys\Res\Servers\ThisServer;
use App\Sys\Res\Types\INewSystemType;
use App\Sys\Res\Types\Stk;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignAttributeCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Server\ServerPromote;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublish;
use App\Sys\Res\Users\SystemUser;
use Illuminate\Support\Facades\DB;


class NewBuild
{
    const MAP_FILE = 'bootstrap/cache/hbc_cache.php';

    /** @type array<INewSystemType>   */
    const array TYPES = [
        Stk\Root::class,
        Stk\Root\About::class,
        Stk\Root\Action::class,
        Stk\Root\Api::class,
        Stk\Root\Container::class,
        Stk\Root\Meta::class,
        Stk\Root\Content::class,
        Stk\Root\Debug::class,
        Stk\Root\Display::class,
        Stk\Root\Event::class,
        Stk\Root\Handle::class,
        Stk\Root\LiveRules::class,
        Stk\Root\Media::class,
        Stk\Root\NamespaceType::class,

        Stk\Root\Phase::class,
        Stk\Root\Marker::class,
        Stk\Root\Server::class,
        Stk\Root\Signal::class,
        Stk\Root\TrackingExported::class,



        Stk\Root\Content\Blog::class,
        Stk\Root\Content\Message::class,
        Stk\Root\Content\News::class,

        Stk\Root\Marker\ChangeOwnershipMarker::class,
        Stk\Root\Marker\DeletionMarker::class,
        Stk\Root\Media\Audio::class,
        Stk\Root\Media\Image::class,
        Stk\Root\Media\Video::class,
        Stk\Root\Meta\Language::class,
        Stk\Root\Meta\Region::class,
        Stk\Root\Meta\Language\En::class,
        Stk\Root\Meta\Region\Us::class,

        Stk\Root\Namespace\NamespaceBase::class,
        Stk\Root\Namespace\DeletingUserMarker::class,
        Stk\Root\Namespace\HomeSet::class,
        Stk\Root\Namespace\PrivateType::class,
        Stk\Root\Namespace\PublicType::class,
        Stk\Root\Namespace\TransferNamespace::class,


        Stk\Root\Server\ThisServerType::class,


        Stk\Root\Phases\AdvicePhase::class,
        Stk\Root\Phases\EditPhase::class,
        Stk\Root\Phases\NormalPhase::class,


        Stk\Root\Signal\Mutex::class,
        Stk\Root\Signal\Semaphore::class,




        Stk\Root\Evt\BaseEvent::class,
        Stk\Root\Evt\EventHandler::class,
        Stk\Root\Evt\ScopeElement::class,
        Stk\Root\Evt\ScopeElsewhere::class,
        Stk\Root\Evt\ScopeServer::class,
        Stk\Root\Evt\ScopeSet::class,
        Stk\Root\Evt\ScopeType::class,
        Stk\Root\Evt\Element\LinkCreated::class,
        Stk\Root\Evt\Element\LinkCreating::class,
        Stk\Root\Evt\Element\NamespaceAdminAdding::class,
        Stk\Root\Evt\Element\NamespaceAdminRemoving::class,
        Stk\Root\Evt\Element\NamespaceLogin::class,
        Stk\Root\Evt\Element\NamespaceMemberAdding::class,
        Stk\Root\Evt\Element\NamespaceMemberRemoving::class,
        Stk\Root\Evt\Element\SearchResults::class,
        Stk\Root\Evt\Elsewhere\ElsewhereAskingElement::class,
        Stk\Root\Evt\Elsewhere\ElsewhereAskingNamespace::class,
        Stk\Root\Evt\Elsewhere\ElsewhereAskingSet::class,
        Stk\Root\Evt\Elsewhere\ElsewhereAskingType::class,
        Stk\Root\Evt\Elsewhere\ElsewhereCredentialsAsking::class,
        Stk\Root\Evt\Elsewhere\ElsewhereCredentialsBad::class,
        Stk\Root\Evt\Elsewhere\ElsewhereCredentialsNew::class,
        Stk\Root\Evt\Elsewhere\ElsewhereCredentialsSending::class,
        Stk\Root\Evt\Elsewhere\ElsewhereDestroyingElement::class,
        Stk\Root\Evt\Elsewhere\ElsewhereElementReentered::class,
        Stk\Root\Evt\Elsewhere\ElsewhereGivesElement::class,
        Stk\Root\Evt\Elsewhere\ElsewhereGivesEvent::class,
        Stk\Root\Evt\Elsewhere\ElsewhereGivesNamespace::class,
        Stk\Root\Evt\Elsewhere\ElsewhereGivesSet::class,
        Stk\Root\Evt\Elsewhere\ElsewhereGivesType::class,
        Stk\Root\Evt\Elsewhere\ElsewherePushingElement::class,
        Stk\Root\Evt\Elsewhere\ElsewherePushingEvent::class,
        Stk\Root\Evt\Elsewhere\ElsewherePushingNamespace::class,
        Stk\Root\Evt\Elsewhere\ElsewherePushingSet::class,
        Stk\Root\Evt\Elsewhere\ElsewherePushingType::class,
        Stk\Root\Evt\Elsewhere\ElsewhereSharingElement::class,
        Stk\Root\Evt\Elsewhere\ElsewhereSuspendingType::class,
        Stk\Root\Evt\Elsewhere\ServerRegistered::class,
        Stk\Root\Evt\Elsewhere\ServerStatusAllowed::class,
        Stk\Root\Evt\Elsewhere\ServerStatusBlocked::class,
        Stk\Root\Evt\Elsewhere\ServerStatusPaused::class,
        Stk\Root\Evt\Elsewhere\ServerStatusPending::class,
        Stk\Root\Evt\Server\CustomEventFired::class,
        Stk\Root\Evt\Server\LinkDestroyed::class,
        Stk\Root\Evt\Server\LinkDestroying::class,
        Stk\Root\Evt\Server\NamespaceCreated::class,
        Stk\Root\Evt\Server\NamespaceDestroyed::class,
        Stk\Root\Evt\Server\NamespaceStartingTransfer::class,
        Stk\Root\Evt\Server\NamespaceTransfered::class,
        Stk\Root\Evt\Server\PathHandleAdded::class,
        Stk\Root\Evt\Server\PathHandleRemoved::class,
        Stk\Root\Evt\Server\PhaseAdded::class,
        Stk\Root\Evt\Server\PhasePurged::class,
        Stk\Root\Evt\Server\ServerEdited::class,
        Stk\Root\Evt\Server\SetCreated::class,
        Stk\Root\Evt\Server\SetDestroyed::class,
        Stk\Root\Evt\Server\SetDestroying::class,
        Stk\Root\Evt\Server\TypeDeleted::class,
        Stk\Root\Evt\Server\TypeHandleAdded::class,
        Stk\Root\Evt\Server\TypeHandleRemoved::class,
        Stk\Root\Evt\Server\TypeOwnerChanged::class,
        Stk\Root\Evt\Server\TypeOwnerChanging::class,
        Stk\Root\Evt\Server\TypeRetired::class,
        Stk\Root\Evt\Server\TypeSuspended::class,
        Stk\Root\Evt\Server\UserDeletionPreparing::class,
        Stk\Root\Evt\Server\UserDeletionStarting::class,
        Stk\Root\Evt\Server\UserEdit::class,
        Stk\Root\Evt\Server\UserLoggingIn::class,
        Stk\Root\Evt\Server\UserRegistered::class,
        Stk\Root\Evt\Set\AttributeWrite::class,
        Stk\Root\Evt\Set\LiveTypeAdded::class,
        Stk\Root\Evt\Set\LiveTypePasted::class,
        Stk\Root\Evt\Set\LiveTypeRemoved::class,
        Stk\Root\Evt\Set\MapEntered::class,
        Stk\Root\Evt\Set\MapLeft::class,
        Stk\Root\Evt\Set\Reading::class,
        Stk\Root\Evt\Set\SetChildCreated::class,
        Stk\Root\Evt\Set\SetChildDestroyed::class,
        Stk\Root\Evt\Set\SetEntered::class,
        Stk\Root\Evt\Set\SetEntering::class,
        Stk\Root\Evt\Set\SetLeaving::class,
        Stk\Root\Evt\Set\SetLeft::class,
        Stk\Root\Evt\Set\ShapeEntered::class,
        Stk\Root\Evt\Set\ShapeLeft::class,
        Stk\Root\Evt\Set\SwitchedOff::class,
        Stk\Root\Evt\Set\SwitchedOn::class,
        Stk\Root\Evt\Set\SwitchingOff::class,
        Stk\Root\Evt\Set\SwitchingOn::class,
        Stk\Root\Evt\Set\TypeMapEnclosedEnd::class,
        Stk\Root\Evt\Set\TypeMapEnclosedStart::class,
        Stk\Root\Evt\Set\TypeShapeEnclosedEnd::class,
        Stk\Root\Evt\Set\TypeShapeEnclosedStart::class,
        Stk\Root\Evt\Type\AttributePending::class,
        Stk\Root\Evt\Type\DesignParentAdding::class,
        Stk\Root\Evt\Type\ElementCreation::class,
        Stk\Root\Evt\Type\ElementDestroyed::class,
        Stk\Root\Evt\Type\ElementDestruction::class,
        Stk\Root\Evt\Type\ElementOwnerChange::class,
        Stk\Root\Evt\Type\ElementPhaseChangeBatch::class,
        Stk\Root\Evt\Type\ElementRecieved::class,
        Stk\Root\Evt\Type\PhaseChangedQuiet::class,
        Stk\Root\Evt\Type\PhaseTreeCopied::class,
        Stk\Root\Evt\Type\PhaseTreeDeleted::class,
        Stk\Root\Evt\Type\PhaseTreeMoved::class,
        Stk\Root\Evt\Type\SetCreating::class,
        Stk\Root\Evt\Type\TypePublishing::class,



        Stk\Root\Act\Cmd::class,
        Stk\Root\Act\CmdNoSideEffects::class,
        Stk\Root\Act\NoEventsTriggered::class,
        Stk\Root\Act\Pragma::class,
        Stk\Root\Act\SystemPrivilege::class,
        Stk\Root\Act\Cmd\Ds::class,
        Stk\Root\Act\Cmd\Ele::class,
        Stk\Root\Act\Cmd\Ew::class,
        Stk\Root\Act\Cmd\Ns::class,
        Stk\Root\Act\Cmd\Op::class,
        Stk\Root\Act\Cmd\Pa::class,
        Stk\Root\Act\Cmd\Ph::class,
        Stk\Root\Act\Cmd\Server::class,
        Stk\Root\Act\Cmd\St::class,
        Stk\Root\Act\Cmd\Time::class,
        Stk\Root\Act\Cmd\Ty::class,
        Stk\Root\Act\Cmd\Us::class,



        Stk\Root\Act\Cmd\Ds\DesignAttributeCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignAttributeDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignAttributeEdit::class,
        Stk\Root\Act\Cmd\Ds\DesignCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignEdit::class,
        Stk\Root\Act\Cmd\Ds\DesignListenerCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignListenerDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignListenerTest::class,
        Stk\Root\Act\Cmd\Ds\DesignLiveRuleAdd::class,
        Stk\Root\Act\Cmd\Ds\DesignLiveRuleRemove::class,
        Stk\Root\Act\Cmd\Ds\DesignLocationCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignLocationDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignLocationEdit::class,
        Stk\Root\Act\Cmd\Ds\DesignOwnerChange::class,
        Stk\Root\Act\Cmd\Ds\DesignParentAdd::class,
        Stk\Root\Act\Cmd\Ds\DesignParentRemove::class,
        Stk\Root\Act\Cmd\Ds\DesignRuleCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignRuleDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignRuleEdit::class,
        Stk\Root\Act\Cmd\Ds\DesignTimeCreate::class,
        Stk\Root\Act\Cmd\Ds\DesignTimeDestroy::class,
        Stk\Root\Act\Cmd\Ds\DesignTimeEdit::class,
        Stk\Root\Act\Cmd\Ele\ElementDestroy::class,
        Stk\Root\Act\Cmd\Ele\ElementOwnerChange::class,
        Stk\Root\Act\Cmd\Ele\ElementPhaseChange::class,
        Stk\Root\Act\Cmd\Ele\ElementPing::class,
        Stk\Root\Act\Cmd\Ele\LinkAdd::class,
        Stk\Root\Act\Cmd\Ele\LinkRemove::class,
        Stk\Root\Act\Cmd\Ele\LiveTypeAdd::class,
        Stk\Root\Act\Cmd\Ele\LiveTypeCopy::class,
        Stk\Root\Act\Cmd\Ele\LiveTypeDemote::class,
        Stk\Root\Act\Cmd\Ele\LiveTypePromote::class,
        Stk\Root\Act\Cmd\Ele\LiveTypeRemove::class,
        Stk\Root\Act\Cmd\Ele\Read::class,
        Stk\Root\Act\Cmd\Ele\ReadTimeSpan::class,
        Stk\Root\Act\Cmd\Ele\SetCreate::class,
        Stk\Root\Act\Cmd\Ele\SwitchOff::class,
        Stk\Root\Act\Cmd\Ele\SwitchOn::class,
        Stk\Root\Act\Cmd\Ele\Write::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereAskCredentials::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereAskElement::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereAskNamespace::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereAskSet::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereAskType::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereChangeStatus::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereDestroyedElement::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereDoRegistration::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveCredentials::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveElement::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveEvent::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveNamespace::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveSet::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereGiveType::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePurge::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushCredentials::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushElement::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushEvent::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushNamespace::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushSet::class,
        Stk\Root\Act\Cmd\Ew\ElsewherePushType::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereSharingElement::class,
        Stk\Root\Act\Cmd\Ew\ElsewhereSuspendedType::class,
        Stk\Root\Act\Cmd\Ns\NamespaceAdminAdd::class,
        Stk\Root\Act\Cmd\Ns\NamespaceAdminPromote::class,
        Stk\Root\Act\Cmd\Ns\NamespaceAdminPurge::class,
        Stk\Root\Act\Cmd\Ns\NamespaceAdminRemove::class,
        Stk\Root\Act\Cmd\Ns\NamespaceCreate::class,
        Stk\Root\Act\Cmd\Ns\NamespaceDeletionStart::class,
        Stk\Root\Act\Cmd\Ns\NamespaceDestroy::class,
        Stk\Root\Act\Cmd\Ns\NamespaceMemberAdd::class,
        Stk\Root\Act\Cmd\Ns\NamespaceMemberPromote::class,
        Stk\Root\Act\Cmd\Ns\NamespaceMemberPurge::class,
        Stk\Root\Act\Cmd\Ns\NamespaceMemberRemove::class,
        Stk\Root\Act\Cmd\Ns\NamespacePromote::class,
        Stk\Root\Act\Cmd\Ns\NamespacePurge::class,
        Stk\Root\Act\Cmd\Ns\NamespaceTransferDo::class,
        Stk\Root\Act\Cmd\Ns\NamespaceTransferPre::class,
        Stk\Root\Act\Cmd\Op\Combine::class,
        Stk\Root\Act\Cmd\Op\Mutual::class,
        Stk\Root\Act\Cmd\Op\Pop::class,
        Stk\Root\Act\Cmd\Op\Push::class,
        Stk\Root\Act\Cmd\Op\Shift::class,
        Stk\Root\Act\Cmd\Op\Unshift::class,
        Stk\Root\Act\Cmd\Pa\PathCopy::class,
        Stk\Root\Act\Cmd\Pa\PathCreate::class,
        Stk\Root\Act\Cmd\Pa\PathDestroy::class,
        Stk\Root\Act\Cmd\Pa\PathEdit::class,
        Stk\Root\Act\Cmd\Pa\PathHandleAdd::class,
        Stk\Root\Act\Cmd\Pa\PathHandleRemove::class,
        Stk\Root\Act\Cmd\Pa\PathPartCreate::class,
        Stk\Root\Act\Cmd\Pa\PathPartDestroy::class,
        Stk\Root\Act\Cmd\Pa\PathPartEdit::class,
        Stk\Root\Act\Cmd\Pa\PathPartTest::class,
        Stk\Root\Act\Cmd\Pa\PathPublish::class,
        Stk\Root\Act\Cmd\Pa\PathTest::class,
        Stk\Root\Act\Cmd\Pa\Search::class,
        Stk\Root\Act\Cmd\Ph\PhaseCreate::class,
        Stk\Root\Act\Cmd\Ph\PhaseTreeDelete::class,
        Stk\Root\Act\Cmd\Ph\PhaseTreeCopy::class,
        Stk\Root\Act\Cmd\Ph\PhaseTreeMove::class,
        Stk\Root\Act\Cmd\Ph\PhasePurge::class,
        Stk\Root\Act\Cmd\Server\ServerEdit::class,
        Stk\Root\Act\Cmd\Server\ServerPromote::class,
        Stk\Root\Act\Cmd\Server\ServerShow::class,
        Stk\Root\Act\Cmd\Server\ServerShowAdmin::class,
        Stk\Root\Act\Cmd\St\SetDestroy::class,
        Stk\Root\Act\Cmd\St\SetEmpty::class,
        Stk\Root\Act\Cmd\St\SetMemberAdd::class,
        Stk\Root\Act\Cmd\St\SetMemberPurge::class,
        Stk\Root\Act\Cmd\St\SetMemberRemove::class,
        Stk\Root\Act\Cmd\St\SetMemberStick::class,
        Stk\Root\Act\Cmd\St\SetMemberUnstick::class,
        Stk\Root\Act\Cmd\St\SetPurge::class,
        Stk\Root\Act\Cmd\Ty\ElementCreate::class,
        Stk\Root\Act\Cmd\Ty\FireCustomEvent::class,
        Stk\Root\Act\Cmd\Ty\TypeDestroy::class,
        Stk\Root\Act\Cmd\Ty\TypeHandleAdd::class,
        Stk\Root\Act\Cmd\Ty\TypeHandleRemove::class,
        Stk\Root\Act\Cmd\Ty\TypeOwnerChange::class,
        Stk\Root\Act\Cmd\Ty\TypeOwnerPromote::class,
        Stk\Root\Act\Cmd\Ty\TypePublish::class,
        Stk\Root\Act\Cmd\Ty\TypePurge::class,
        Stk\Root\Act\Cmd\Ty\TypeRetire::class,
        Stk\Root\Act\Cmd\Ty\TypeSuspend::class,
        Stk\Root\Act\Cmd\Us\UserEdit::class,
        Stk\Root\Act\Cmd\Us\UserLogin::class,
        Stk\Root\Act\Cmd\Us\UserPrepareDeletion::class,
        Stk\Root\Act\Cmd\Us\UserRegister::class,
        Stk\Root\Act\Cmd\Us\UserStartDeletion::class,







        Stk\Root\Api\DesignApi::class,
        Stk\Root\Api\ElementApi::class,
        Stk\Root\Api\ElsewhereApi::class,
        Stk\Root\Api\NamespaceApi::class,
        Stk\Root\Api\OperationApi::class,
        Stk\Root\Api\PathApi::class,
        Stk\Root\Api\PhaseApi::class,
        Stk\Root\Api\ServerApi::class,
        Stk\Root\Api\SetApi::class,
        Stk\Root\Api\TypeApi::class,
        Stk\Root\Api\UserApi::class,
        Stk\Root\Api\Design\AddLiveRule::class,
        Stk\Root\Api\Design\AddParent::class,
        Stk\Root\Api\Design\ChangeOwner::class,
        Stk\Root\Api\Design\Create::class,
        Stk\Root\Api\Design\CreateAttribute::class,
        Stk\Root\Api\Design\CreateListener::class,
        Stk\Root\Api\Design\CreateListenerRule::class,
        Stk\Root\Api\Design\CreateLocation::class,
        Stk\Root\Api\Design\CreateTime::class,
        Stk\Root\Api\Design\Destroy::class,
        Stk\Root\Api\Design\DestroyAttribute::class,
        Stk\Root\Api\Design\DestroyListener::class,
        Stk\Root\Api\Design\DestroyListenerRule::class,
        Stk\Root\Api\Design\DestroyLocation::class,
        Stk\Root\Api\Design\DestroyTime::class,
        Stk\Root\Api\Design\Edit::class,
        Stk\Root\Api\Design\EditAttribute::class,
        Stk\Root\Api\Design\EditListenerRule::class,
        Stk\Root\Api\Design\EditLocation::class,
        Stk\Root\Api\Design\EditTime::class,
        Stk\Root\Api\Design\ListAttributes::class,
        Stk\Root\Api\Design\ListDesigns::class,
        Stk\Root\Api\Design\ListListeners::class,
        Stk\Root\Api\Design\ListLiveRules::class,
        Stk\Root\Api\Design\ListLocations::class,
        Stk\Root\Api\Design\ListSchedules::class,
        Stk\Root\Api\Design\RemoveLiveRule::class,
        Stk\Root\Api\Design\RemoveParent::class,
        Stk\Root\Api\Design\ShowAttribute::class,
        Stk\Root\Api\Design\ShowDesign::class,
        Stk\Root\Api\Design\ShowListener::class,
        Stk\Root\Api\Design\ShowLocation::class,
        Stk\Root\Api\Design\ShowTime::class,
        Stk\Root\Api\Design\TestListener::class,
        Stk\Root\Api\Element\AddLive::class,
        Stk\Root\Api\Element\ChangeOwner::class,
        Stk\Root\Api\Element\ChangePhase::class,
        Stk\Root\Api\Element\CopyLive::class,
        Stk\Root\Api\Element\CreateSet::class,
        Stk\Root\Api\Element\DemoteLive::class,
        Stk\Root\Api\Element\Destroy::class,
        Stk\Root\Api\Element\Link::class,
        Stk\Root\Api\Element\ListLinks::class,
        Stk\Root\Api\Element\Ping::class,
        Stk\Root\Api\Element\PromoteLive::class,
        Stk\Root\Api\Element\PromoteSet::class,
        Stk\Root\Api\Element\Read::class,
        Stk\Root\Api\Element\ReadLiveType::class,
        Stk\Root\Api\Element\ReadTime::class,
        Stk\Root\Api\Element\RemoveLive::class,
        Stk\Root\Api\Element\ShowLink::class,
        Stk\Root\Api\Element\ShowPublic::class,
        Stk\Root\Api\Element\SwitchOff::class,
        Stk\Root\Api\Element\SwitchOn::class,
        Stk\Root\Api\Element\UnLink::class,
        Stk\Root\Api\Element\WriteAttribute::class,
        Stk\Root\Api\Elsewhere\AskCredentials::class,
        Stk\Root\Api\Elsewhere\AskElement::class,
        Stk\Root\Api\Elsewhere\AskNamespace::class,
        Stk\Root\Api\Elsewhere\AskSet::class,
        Stk\Root\Api\Elsewhere\AskType::class,
        Stk\Root\Api\Elsewhere\ChangeStatus::class,
        Stk\Root\Api\Elsewhere\DestroyedElement::class,
        Stk\Root\Api\Elsewhere\GiveCredentials::class,
        Stk\Root\Api\Elsewhere\GiveElement::class,
        Stk\Root\Api\Elsewhere\GiveEvent::class,
        Stk\Root\Api\Elsewhere\GiveNamespace::class,
        Stk\Root\Api\Elsewhere\GiveSet::class,
        Stk\Root\Api\Elsewhere\GiveType::class,
        Stk\Root\Api\Elsewhere\ListElsewhere::class,
        Stk\Root\Api\Elsewhere\Purge::class,
        Stk\Root\Api\Elsewhere\PushCredentials::class,
        Stk\Root\Api\Elsewhere\PushElement::class,
        Stk\Root\Api\Elsewhere\PushEvent::class,
        Stk\Root\Api\Elsewhere\PushNamespace::class,
        Stk\Root\Api\Elsewhere\PushSet::class,
        Stk\Root\Api\Elsewhere\PushType::class,
        Stk\Root\Api\Elsewhere\Register::class,
        Stk\Root\Api\Elsewhere\ShareElement::class,
        Stk\Root\Api\Elsewhere\Show::class,
        Stk\Root\Api\Elsewhere\ShowAdmin::class,
        Stk\Root\Api\Elsewhere\SuspendedType::class,
        Stk\Root\Api\Namespace\AddAdmin::class,
        Stk\Root\Api\Namespace\AddMember::class,
        Stk\Root\Api\Namespace\Create::class,
        Stk\Root\Api\Namespace\Destroy::class,
        Stk\Root\Api\Namespace\ListAdmins::class,
        Stk\Root\Api\Namespace\ListNamespaces::class,
        Stk\Root\Api\Namespace\ListMembers::class,
        Stk\Root\Api\Namespace\Promote::class,
        Stk\Root\Api\Namespace\PromoteAdmin::class,
        Stk\Root\Api\Namespace\PromoteMember::class,
        Stk\Root\Api\Namespace\Purge::class,
        Stk\Root\Api\Namespace\PurgeAdmin::class,
        Stk\Root\Api\Namespace\PurgeMember::class,
        Stk\Root\Api\Namespace\RemoveAdmin::class,
        Stk\Root\Api\Namespace\RemoveMember::class,
        Stk\Root\Api\Namespace\Show::class,
        Stk\Root\Api\Namespace\StartDeletion::class,
        Stk\Root\Api\Namespace\StartTransfer::class,
        Stk\Root\Api\Namespace\TransferOwner::class,
        Stk\Root\Api\Operation\Combine::class,
        Stk\Root\Api\Operation\Mutual::class,
        Stk\Root\Api\Operation\Pop::class,
        Stk\Root\Api\Operation\Push::class,
        Stk\Root\Api\Operation\Shift::class,
        Stk\Root\Api\Operation\Unshift::class,
        Stk\Root\Api\Path\AddHandle::class,
        Stk\Root\Api\Path\Copy::class,
        Stk\Root\Api\Path\Create::class,
        Stk\Root\Api\Path\CreatePart::class,
        Stk\Root\Api\Path\Destroy::class,
        Stk\Root\Api\Path\DestroyPart::class,
        Stk\Root\Api\Path\Edit::class,
        Stk\Root\Api\Path\EditPart::class,
        Stk\Root\Api\Path\ListAll::class,
        Stk\Root\Api\Path\Publish::class,
        Stk\Root\Api\Path\RemoveHandle::class,
        Stk\Root\Api\Path\Search::class,
        Stk\Root\Api\Path\Show::class,
        Stk\Root\Api\Path\ShowPartTree::class,
        Stk\Root\Api\Path\Test::class,
        Stk\Root\Api\Path\TestPart::class,
        Stk\Root\Api\Phase\CopyTree::class,
        Stk\Root\Api\Phase\Create::class,
        Stk\Root\Api\Phase\DeleteTree::class,
        Stk\Root\Api\Phase\ListPhases::class,
        Stk\Root\Api\Phase\MoveTree::class,
        Stk\Root\Api\Phase\Purge::class,
        Stk\Root\Api\Phase\Show::class,
        Stk\Root\Api\Server\Edit::class,
        Stk\Root\Api\Server\Show::class,
        Stk\Root\Api\Server\ShowAdmin::class,
        Stk\Root\Api\Set\AddElement::class,
        Stk\Root\Api\Set\DestroySet::class,
        Stk\Root\Api\Set\EmptySet::class,
        Stk\Root\Api\Set\ListChildren::class,
        Stk\Root\Api\Set\ListMembers::class,
        Stk\Root\Api\Set\ListSets::class,
        Stk\Root\Api\Set\Purge::class,
        Stk\Root\Api\Set\PurgeMember::class,
        Stk\Root\Api\Set\RemoveElement::class,
        Stk\Root\Api\Set\ShowPublic::class,
        Stk\Root\Api\Set\ShowSet::class,
        Stk\Root\Api\Set\StickElement::class,
        Stk\Root\Api\Set\UnstickElement::class,
        Stk\Root\Api\Type\AddHandle::class,
        Stk\Root\Api\Type\ChangeOwner::class,
        Stk\Root\Api\Type\CreateElement::class,
        Stk\Root\Api\Type\DestroyType::class,
        Stk\Root\Api\Type\FireEvent::class,
        Stk\Root\Api\Type\ListAllSuspended::class,
        Stk\Root\Api\Type\ListAttributeDescendants::class,
        Stk\Root\Api\Type\ListDescendants::class,
        Stk\Root\Api\Type\ListElementsOfType::class,
        Stk\Root\Api\Type\ListLive::class,
        Stk\Root\Api\Type\ListPublished::class,
        Stk\Root\Api\Type\ListSuspended::class,
        Stk\Root\Api\Type\PromoteElement::class,
        Stk\Root\Api\Type\PromoteOwner::class,
        Stk\Root\Api\Type\Publish::class,
        Stk\Root\Api\Type\Purge::class,
        Stk\Root\Api\Type\RemoveHandle::class,
        Stk\Root\Api\Type\Retire::class,
        Stk\Root\Api\Type\ShowPublic::class,
        Stk\Root\Api\Type\ShowType::class,
        Stk\Root\Api\Type\Suspend::class,
        Stk\Root\Api\User\Login::class,
        Stk\Root\Api\User\PrepareUserDeletion::class,
        Stk\Root\Api\User\Register::class,
        Stk\Root\Api\User\StartUserDeletion::class

    ];

    public function __construct(
        protected ?\Illuminate\Console\Command $output = null
    )
    {

    }

    /**
     * @throws \Throwable
     */
    public function doUpdateBuild( ) {
        //get any new uuids not in the db for the types
        DB::transaction(function() {
            $uuids = [];

            foreach (static::TYPES as $blueprint) {
                $uuids[] = $blueprint::getTypeUuid();
            }

            $existing_uuids = ElementType::whereIn('ref_uuid', $uuids)->pluck('ref_uuid')->toArray();
            $uuids_to_build = array_diff($uuids, $existing_uuids);

            foreach (static::TYPES as $blueprint) {
                if (in_array($blueprint::getTypeUuid(), $uuids_to_build)) {
                    $this->register_type(info: $blueprint);
                }
            }
        });
    }

    public function doListInOutput( ) {
        $uuids = [];

        foreach (static::TYPES as $blueprint) {
            $uuids[] = $blueprint::getTypeUuid();
        }

        $existing_uuids = ElementType::whereIn('ref_uuid', $uuids)->pluck('ref_uuid')->toArray();
        $uuids_to_build = array_diff($uuids, $existing_uuids);

        foreach (static::TYPES as $blueprint) {
            if (in_array($blueprint::getTypeUuid(), $uuids_to_build)) {
                $this->output?->info(sprintf("New type: %s %s ",$blueprint::getTypeName(),$blueprint::getTypeUuid()));
                foreach ($blueprint::getAttributeClasses() as $attr_blueprint) {
                    $this->output?->info(sprintf("New attribute: %s %s ",$attr_blueprint::getAttributeUuid(),
                        $blueprint::getTypeName().':'.$attr_blueprint::getAttributeName()));
                }
            }
        }
    }

    protected ?Server $server = null;
    protected ?UserNamespace $ns = null;
    protected ?User $user = null;
    /**
     * @throws \Throwable
     */
    public function doNewBuild( ) {
        /*
         * Make system user
         * Make system server without type or namespace
         *
         * Phases create all without a type, then go back and fill in edited by
         * Loop through and make the types, each type makes the attributes with it
         * Fill phases and when their type is made, fill in the types
         * Create system namespace
         * Fill the ns reference to the server, set ns for all types at once
         */
        DB::transaction(function() {
            $this->createUser();
            $this->createServer();
            $this->createPhases();
            foreach (static::TYPES as $blueprint) {
                $this->register_type(info: $blueprint);
            }
            $this->attachTypeToServer();
            $this->setPhaseTypes();
            $this->createNamespace();
            $this->attachUserToNamespace();
            $this->attachServerToNamespace();
            $this->setAllSystemTypesToNamespace();
        });

    }


    /**
     * @throws \Throwable
     */
    function register_type(string|INewSystemType $info) : ElementType {

        $base_type_params = TypeParamData::makingUsingCodeArray([
            'handle_ref_uuid'=>null,
            'type_name'=> $info::getTypeName(),
            'is_final_type'=> $info::isTypeFinal(),
            'access'=> $info::getTypeAccessPolicy(),
        ]);
        $base_type_factory = new DesignCreate(
            params: $base_type_params,
            is_system: true,
            use_ref: $info::getTypeUuid(),
            owner_namespace: null,
            server: $this->server
        );

        $base_type = $base_type_factory->createDesign();

        foreach ($info::getParentUuids() as $parent_uuid) {
            $parent_type = ElementType::getElementType(uuid: $parent_uuid);
            ElementTypeParent::addOrUpdateParent(parent: $parent_type, child: $base_type);
        }

        foreach ($info::getAttributeClasses() as $attr_blueprint) {
            $attr = $this->register_attribute(type: $base_type,info: $attr_blueprint);
            Utilities::ignoreVar($attr);
        }

        $base_type->loadMissing('type_attributes');
        $type =  new TypePublish(given_type: $base_type, caller_namespace: null, do_permission_check: false)->doPublishCall();
        $this->output?->info(sprintf("published type: %s %s %s",$type->id,$type->type_name,$info::getTypeUuid()));
        return $type;
    }

    function register_attribute(ElementType $type, string|INewSystemAttribute $info) : Attribute {
        $params = AttributeParamData::makingUsingCodeArray([
            'parent_ref_uuid' => $info::getAttributeParentUuid(),
            'design_ref_uuid' => $info::getAttributeDesignUuid(),
            'location_uuid' => $info::getAttributeLocationUuid(),
            'attribute_name' => $info::getAttributeName(),
            'is_final_attribute' => $info::isAttributeFinal(),
            'is_abstract' => $info::isAttributeAbstract(),
            'access_policy' => $info::getAccessPolicy(),
            'value_policy' => $info::getValuePolicy(),
            'read_json_path' => $info::getReadJsonPath(),
            'validate_json_path' => $info::getValidateJsonPath(),
            'attribute_default_value' => $info::getAttributeDefaultValue()
        ]);

        $factory = new DesignAttributeCreate(params: $params,is_system: true,use_ref: $info::getAttributeUuid(),
            calling_namespace: null,given_type: $type,do_permission_check: false);
        $att =  $factory->doCreateAttribute();
        $this->output?->info(sprintf("made attribute: %s %s %s",$att->id,$type->type_name.':'.$att->attribute_name,$info::getAttributeUuid()));
        return $att;
    }

    function createServer() {

        $type = ElementType::getElementType(uuid: ThisServer::getServerTypeUUID(),b_throw_if_missing: false);
        $this->server =  ServerPromote::createServer(
            given_type: $type,
            given_namespace: $this->ns,
            server_name:   ThisServer::getServerName(),
            server_domain: ThisServer::getServerDomain(),
            server_url: ThisServer::getServerUrl(),
            server_status: ThisServer::getServerStatus(),
            uuid: ThisServer::getServerUuid(),
            is_system: true
        );
        $this->output?->info(sprintf("made server: %s %s ",$this->server->id,$this->server->server_name));
    }

    function attachTypeToServer() : void {
        $type = ElementType::getElementType(uuid: ThisServer::getServerTypeUUID());
        $this->server->server_type_id = $type->id;
        $this->server->save();
        $this->output?->info("attached type to server");
    }

    function createPhases() {
        //create all three without a type
        /** @type array<Stk\Root\Phase> $phase_blueprints */
        $phase_blueprints = [
            'normal'=> Stk\Root\Phases\NormalPhase::class,
            'edit'=> Stk\Root\Phases\EditPhase::class,
            'advice'=>Stk\Root\Phases\AdvicePhase::class
        ];
        /** @type array<Phase> $phases */
        $phases = [
            'normal'=>null,'edit'=>null,'advice'=>null
        ];
        foreach ($phase_blueprints as $index => $phase_blueprint) {
            $phase = new Phase();
            $phase->ref_uuid = $phase_blueprint::PHASE_UUID;
            $phase->setPhaseName($phase_blueprint::getTypeName());
            $phase->is_default_phase = ($index === 'normal');
            $phase->is_system = true;
            $phase->save();
            $this->output?->info(sprintf("made phase: #%s %s ",$phase->id,$phase->phase_name));
            $phases[$index] = $phase;
        }

        $phases['normal']->edited_by_phase_id  = $phases['edit']->id;
        $phases['normal']->save();

        $phases['edit']->edited_by_phase_id  = $phases['advice']->id;
        $phases['edit']->save();

        $phases['advice']->edited_by_phase_id  = $phases['normal']->id;
        $phases['advice']->save();
    }

    function setPhaseTypes() {

        $phase = Phase::getThisPhase(uuid:Stk\Root\Phases\NormalPhase::PHASE_UUID);
        $phase->phase_type_id = ElementType::getElementType(uuid: Stk\Root\Phases\NormalPhase::getTypeUuid())->id;
        $phase->save();


        $phase = Phase::getThisPhase(uuid:Stk\Root\Phases\EditPhase::PHASE_UUID);
        $phase->phase_type_id = ElementType::getElementType(uuid: Stk\Root\Phases\EditPhase::getTypeUuid())->id;
        $phase->save();

        $phase = Phase::getThisPhase(uuid:Stk\Root\Phases\AdvicePhase::PHASE_UUID);
        $phase->phase_type_id = ElementType::getElementType(uuid: Stk\Root\Phases\AdvicePhase::getTypeUuid())->id;
        $phase->save();
        $this->output?->info("attached types to phases");
    }

    /**
     * @throws \Throwable
     */
    function createNamespace() {
        $params = NamespaceParamData::makingUsingCodeArray(['name'=>SystemNamespace::getNamespaceName(),'public_key'=>SystemNamespace::getNamespacePublicKey()]);
        $namespace_factory = new NamespaceCreate(params: $params,given_user: $this->user,given_server: $this->server,is_system: true);
        $preset = new NamespacePresetUuids;
        $preset->home_set_uuid = SystemNamespace::getHomeSetUuid();
        $preset->public_element_uuid = SystemNamespace::getPublicElementUuid();
        $preset->private_element_uuid = SystemNamespace::getPrivateElementUuid();
        $preset->home_element_uuid = SystemNamespace::getHomeSetElementUuid();
        $preset->base_type_uuid = SystemNamespace::getBaseTypeUuid();
        $preset->namespace_uuid = SystemNamespace::getNamespaceUuid();
        $this->ns = $namespace_factory->makeNamespace(preset: $preset);
        $this->output?->info(sprintf("made namespace: %s %s ",$this->ns->id,$this->ns->namespace_name));
    }

    function setAllSystemTypesToNamespace() {
        ElementType::where('is_system',true)->update(['owner_namespace_id' => $this->ns->id]);
        $this->output?->info("attached ns to system types");
    }

    function attachUserToNamespace() {
        $this->user->default_namespace_id = $this->ns->id;
        $this->user->save();
        $this->output?->info("attached ns to user");
    }

    function attachServerToNamespace() {
        $this->server->owning_namespace_id = $this->ns->id;
        $this->server->save();
        $this->output?->info("attached ns to server");
    }

    function createUser() {
        $this->user = (new CreateNewUser)->create([
            "username" => SystemUser::getUserName(),
            "password" => SystemUser::getUserPassword(),
            "password_confirmation" => SystemUser::getUserPassword()
        ]);

        $this->user->is_system = true;
        $this->user->ref_uuid = SystemUser::getUserUuid();
        $this->user->save();
        $this->output?->info(sprintf("made user: %s %s ",$this->user->id,$this->user->username));
    }


    /** @return array<MapEntry> */
    protected static function getMapData() {
        $ret = [];
        foreach (static::TYPES as $blueprint) {

            $ret[] = new MapEntry(full_class_name: $blueprint,name: $blueprint::getTypeName(), uuid: $blueprint::getTypeUuid());

            foreach ($blueprint::getAttributeClasses() as $attr_blueprint) {
                    $ret[] = new MapEntry(full_class_name: $attr_blueprint,name: $attr_blueprint::getAttributeName(), uuid: $attr_blueprint::getAttributeUuid());
            }

        }
        return $ret;
    }

    public static function doMap() : void {
        $file_path = base_path(static::MAP_FILE);
        $map_data = static::getMapData();
        $pre = '<?php return ';
        if (empty($map_data)) {
            $b_result = file_put_contents($file_path,$pre. ' [];');
            if ($b_result === false) {
                throw new \LogicException("Could not write (C) to $file_path");
            }
            return;
        }
        $out = [];
        foreach ( $map_data as $entry) {
            $out[$entry->uuid] = $entry->toArray() ;
        }

        $all = $pre . json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT) . "\n;"
                |> (fn($x) => str_replace(":", "=>", $x))
                |> (fn($x) => str_replace("}", "]", $x))
                |> (fn($x) => str_replace("{", "[", $x));

        $b_result = file_put_contents($file_path,$all);
        if ($b_result === false) {
            throw new \LogicException("Could not write to $file_path");
        }
    }

    public static function getClassFromUuid(string $uuid) : IDocument|INewSystemType|INewSystemAttribute|null|string  {
        $file_path = base_path(static::MAP_FILE);
        $content = include($file_path);
        $node = $content[$uuid]??null ;
        return $node['class']??null;
    }

}
