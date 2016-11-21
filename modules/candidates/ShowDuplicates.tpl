<?php /* $Id: Questionnaire.tpl 3668 2007-11-21 00:38:50Z brian $ */ ?>
<?php TemplateUtility::printHeader('Candidate - Show duplicates', array( 'js/activity.js', 'js/sorttable.js', 'js/match.js', 'js/lib.js', 'js/pipeline.js', 'js/attachment.js')); ?>

<?php TemplateUtility::printHeaderBlock(); ?>
<?php TemplateUtility::printTabs($this->active); ?>
<div id="main">
    <?php TemplateUtility::printQuickSearch(); ?>

    <div id="contents">
        <table>
            <tr>
                <td width="3%">
                    <img src="images/candidate.gif" width="24" height="24" border="0" alt="Candidates" style="margin-top: 3px;" />&nbsp;
                </td>
                <td><h2>Candidates: Find and remove duplicates</h2></td>
            </tr>
        </table>

        <p class="note">List of duplicates by email</p>


            <?php $highlight = true; ?>
            <?php foreach ($this->results as $email => $duplicates): ?>
                <div><h2><?=$email?></h2>
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border: 1px solid #c0c0c0;">
                    <tr>
                    <?php foreach ($duplicates as $id => $candidate): ?>
                        <td>
                            <h3>
                                <a href="<?php echo(CATSUtility::getIndexName()); ?>?m=candidates&amp;a=show&amp;candidateID=<?php echo($id); ?>">
                                    Candidate Id: <?=$id?>
                                </a>
                                <?php if ($this->accessLevel >= ACCESS_LEVEL_DELETE): ?>
                                <a id="delete_link" href="<?php echo(CATSUtility::getIndexName()); ?>?m=candidates&amp;a=delete&amp;candidateID=<?php echo($id); ?>&amp;redirectToAction=showDuplicates" onclick="javascript:return confirm('Delete this candidate?');">
                                    <img src="images/actions/delete.gif" width="16" height="16" class="absmiddle" alt="delete" border="0" />&nbsp;Delete
                                </a>
                                <?php endif; ?>
                            </h3>
                            <ul>
                                <?php foreach ($candidate['data'] as $key => $value): ?>
                                    <?php if (!empty($value)): ?>
                                        <li><?=$key?>: <?=$value?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (!empty($candidate['extra'])): ?>
                                <ul>
                                    <?php foreach ($candidate['extra'] as $key => $value): ?>
                                    <?php if (!empty($value)): ?>
                                    <li><?=$key?>: <?=$value?></li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    </tr>
                </table>
                </div>
            <?php endforeach; ?>
    </div>
</div>

<?php TemplateUtility::printFooter(); ?>
