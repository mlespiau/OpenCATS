<?php
namespace OpenCATS\Controller;
use Symfony\Component\HttpFoundation\Request;

include_once('./lib/Candidates.php');

class CandidateController
{
    private $hooks;
    private $template;

    /* Maximum number of characters of the candidate notes to show without the
     * user clicking "[More]"
     */
    const NOTES_MAXLEN = 500;

    function __construct(\Template $template, \Hooks $hooks)
    {
        $this->template = $template;
        $this->hooks = $hooks;
    }

    /*
     * Called by handleRequest() to process loading the details page.
     */
    public function show(Request $request)
    {
        /* Is this a popup? */
        if (isset($_GET['display']) && $_GET['display'] == 'popup')
        {
            $isPopup = true;
        }
        else
        {
            $isPopup = false;
        }
        $siteId = isset($_SESSION['CATS']) ? $_SESSION['CATS']->getSiteID() : -1;
        $accessLevel = isset($_SESSION['CATS']) ? $_SESSION['CATS']->getAccessLevel() : -1;
        $candidates = new \Candidates($siteId);
        try {
            if (isset($_GET['candidateID']))
            {
                $candidateID = $_GET['candidateID'];
            }
            else
            {
                $candidateID = $candidates->getIDByEmail($_GET['email']);
            }
            $data = $candidates->get($candidateID);
        } catch (Exception $e) {
            CommonErrors::fatal(COMMONERROR_BADINDEX, $this, $e->getMessage());
        }
        /* Bail out if we got an empty result set. */
        if (empty($data))
        {
            CommonErrors::fatal(COMMONERROR_BADINDEX, $this, 'The specified candidate ID could not be found.');
            return;
        }

        if ($data['isAdminHidden'] == 1 && $accessLevel < ACCESS_LEVEL_MULTI_SA)
        {
            // FIXME: Temporary hack for reusing CandidateUI code
            throw new Exception('isHiddenAndNotAdminException');
        }

        /* We want to handle formatting the city and state here instead
         * of in the template.
         */
        $data['cityAndState'] = \StringUtility::makeCityStateString(
            $data['city'], $data['state']
        );

        /*
         * Replace newlines with <br />, fix HTML "special" characters, and
         * strip leading empty lines and spaces.
         */
        $data['notes'] = trim(
            nl2br(htmlspecialchars($data['notes'], ENT_QUOTES))
        );

        /* Chop $data['notes'] to make $data['shortNotes']. */
        if (strlen($data['notes']) > self::NOTES_MAXLEN)
        {
            $data['shortNotes']  = substr(
                $data['notes'], 0, self::NOTES_MAXLEN
            );
            $isShortNotes = true;
        }
        else
        {
            $data['shortNotes'] = $data['notes'];
            $isShortNotes = false;
        }

        /* Format "can relocate" status. */
        if ($data['canRelocate'] == 1)
        {
            $data['canRelocate'] = 'Yes';
        }
        else
        {
            $data['canRelocate'] = 'No';
        }

        if ($data['isHot'] == 1)
        {
            $data['titleClass'] = 'jobTitleHot';
        }
        else
        {
            $data['titleClass'] = 'jobTitleCold';
        }

        $attachments = new \Attachments($siteId);
        $attachmentsRS = $attachments->getAll(
            DATA_ITEM_CANDIDATE, $candidateID
        );

        foreach ($attachmentsRS as $rowNumber => $attachmentsData)
        {
            /* If profile image is not local, force it to be local. */
            if ($attachmentsData['isProfileImage'] == 1)
            {
                $attachments->forceAttachmentLocal($attachmentsData['attachmentID']);
            }

            /* Show an attachment icon based on the document's file type. */
            $attachmentIcon = strtolower(
                \FileUtility::getAttachmentIcon(
                    $attachmentsRS[$rowNumber]['originalFilename']
                )
            );

            $attachmentsRS[$rowNumber]['attachmentIcon'] = $attachmentIcon;

            /* If the text field has any text, show a preview icon. */
            if ($attachmentsRS[$rowNumber]['hasText'])
            {
                $attachmentsRS[$rowNumber]['previewLink'] = sprintf(
                    '<a href="#" onclick="window.open(\'%s?m=candidates&amp;a=viewResume&amp;attachmentID=%s\', \'viewResume\', \'scrollbars=1,width=800,height=760\')"><img width="15" height="15" style="border: none;" src="images/search.gif" alt="(Preview)" /></a>',
                    \CATSUtility::getIndexName(),
                    $attachmentsRS[$rowNumber]['attachmentID']
                );
            }
            else
            {
                $attachmentsRS[$rowNumber]['previewLink'] = '&nbsp;';
            }
        }
        $pipelines = new \Pipelines($siteId);
        $pipelinesRS = $pipelines->getCandidatePipeline($candidateID);

        $sessionCookie = $_SESSION['CATS']->getCookie();

        /* Format pipeline data. */
        foreach ($pipelinesRS as $rowIndex => $row)
        {
            /* Hot jobs [can] have different title styles than normal
             * jobs.
             */
            if ($row['isHot'] == 1)
            {
                $pipelinesRS[$rowIndex]['linkClass'] = 'jobLinkHot';
            }
            else
            {
                $pipelinesRS[$rowIndex]['linkClass'] = 'jobLinkCold';
            }

            $pipelinesRS[$rowIndex]['ownerAbbrName'] = \StringUtility::makeInitialName(
                $pipelinesRS[$rowIndex]['ownerFirstName'],
                $pipelinesRS[$rowIndex]['ownerLastName'],
                false,
                LAST_NAME_MAXLEN
            );

            $pipelinesRS[$rowIndex]['addedByAbbrName'] = \StringUtility::makeInitialName(
                $pipelinesRS[$rowIndex]['addedByFirstName'],
                $pipelinesRS[$rowIndex]['addedByLastName'],
                false,
                LAST_NAME_MAXLEN
            );

            $pipelinesRS[$rowIndex]['ratingLine'] = \TemplateUtility::getRatingObject(
                $pipelinesRS[$rowIndex]['ratingValue'],
                $pipelinesRS[$rowIndex]['candidateJobOrderID'],
                $sessionCookie
            );
        }

        $activityEntries = new \ActivityEntries($siteId);
        $activityRS = $activityEntries->getAllByDataItem($candidateID, DATA_ITEM_CANDIDATE);
        if (!empty($activityRS))
        {
            foreach ($activityRS as $rowIndex => $row)
            {
                if (empty($activityRS[$rowIndex]['notes']))
                {
                    $activityRS[$rowIndex]['notes'] = '(No Notes)';
                }

                if (empty($activityRS[$rowIndex]['jobOrderID']) ||
                    empty($activityRS[$rowIndex]['regarding']))
                {
                    $activityRS[$rowIndex]['regarding'] = 'General';
                }

                $activityRS[$rowIndex]['enteredByAbbrName'] = \StringUtility::makeInitialName(
                    $activityRS[$rowIndex]['enteredByFirstName'],
                    $activityRS[$rowIndex]['enteredByLastName'],
                    false,
                    LAST_NAME_MAXLEN
                );
            }
        }

        /* Get upcoming calendar entries. */
        $calendarRS = $candidates->getUpcomingEvents($candidateID);
        if (!empty($calendarRS))
        {
            foreach ($calendarRS as $rowIndex => $row)
            {
                $calendarRS[$rowIndex]['enteredByAbbrName'] = StringUtility::makeInitialName(
                    $calendarRS[$rowIndex]['enteredByFirstName'],
                    $calendarRS[$rowIndex]['enteredByLastName'],
                    false,
                    LAST_NAME_MAXLEN
                );
            }
        }

        /* Get extra fields. */
        $extraFieldRS = $candidates->extraFields->getValuesForShow($candidateID);

        /* Add an MRU entry. */
        $_SESSION['CATS']->getMRU()->addEntry(
            DATA_ITEM_CANDIDATE, $candidateID, $data['firstName'] . ' ' . $data['lastName']
        );

        /* Is the user an admin - can user see history? */
        if ($accessLevel < ACCESS_LEVEL_DEMO)
        {
            $privledgedUser = false;
        }
        else
        {
            $privledgedUser = true;
        }

        $EEOSettings = new \EEOSettings($siteId);
        $EEOSettingsRS = $EEOSettings->getAll();
        $EEOValues = array();

        /* Make a list of all EEO related values so they can be positioned by index
         * rather than static positioning (like extra fields). */
        if ($EEOSettingsRS['enabled'] == 1)
        {
            if ($EEOSettingsRS['genderTracking'] == 1)
            {
                $EEOValues[] = array('fieldName' => 'Gender', 'fieldValue' => $data['eeoGenderText']);
            }
            if ($EEOSettingsRS['ethnicTracking'] == 1)
            {
                $EEOValues[] = array('fieldName' => 'Ethnicity', 'fieldValue' => $data['eeoEthnicType']);
            }
            if ($EEOSettingsRS['veteranTracking'] == 1)
            {
                $EEOValues[] = array('fieldName' => 'Veteran Status', 'fieldValue' => $data['eeoVeteranType']);
            }
            if ($EEOSettingsRS['disabilityTracking'] == 1)
            {
                $EEOValues[] = array('fieldName' => 'Disability Status', 'fieldValue' => $data['eeoDisabilityStatus']);
            }
        }

        $tags = new \Tags($siteId);

        $questionnaire = new \Questionnaire($siteId);
        $questionnaires = $questionnaire->getCandidateQuestionnaires($candidateID);

        $this->template->assign('accessLevel', $accessLevel);
        $this->template->assign('questionnaires', $questionnaires);
        $this->template->assign('data', $data);
        $this->template->assign('isShortNotes', $isShortNotes);
        $this->template->assign('attachmentsRS', $attachmentsRS);
        $this->template->assign('pipelinesRS', $pipelinesRS);
        $this->template->assign('activityRS', $activityRS);
        $this->template->assign('calendarRS', $calendarRS);
        $this->template->assign('extraFieldRS', $extraFieldRS);
        $this->template->assign('candidateID', $candidateID);
        $this->template->assign('isPopup', $isPopup);
        $this->template->assign('EEOSettingsRS', $EEOSettingsRS);
        $this->template->assign('EEOValues', $EEOValues);
        $this->template->assign('privledgedUser', $privledgedUser);
        $this->template->assign('sessionCookie', $_SESSION['CATS']->getCookie());
        $this->template->assign('tagsRS', $tags->getAll());
        $this->template->assign('assignedTags', $tags->getCandidateTagsTitle($candidateID));

        if (!eval($this->hooks->get('CANDIDATE_SHOW'))) return;

        $this->template->display('./modules/candidates/Show.tpl');
    }

}