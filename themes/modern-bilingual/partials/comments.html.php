<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Local comments section — modern-bilingual
 * Expects: $urlpath (string), $tree (array), $count (int)
 */
$fa = (mb_lang_code() === 'fa');
$submitUrl = site_url() . 'comments/submit';
$countLabel = $count === 1
    ? ($fa ? '۱ دیدگاه' : '1 comment')
    : ($fa ? $count . ' دیدگاه' : $count . ' comments');
$emptyTitle = $fa ? 'هنوز دیدگاهی ثبت نشده' : 'No comments yet';
$emptyHint = $fa ? 'اولین نفری باشید که نظر می‌دهد.' : 'Be the first to share your thoughts.';
$composerTitle = i18n('Leave_a_comment');
$writeCta = $fa ? 'نوشتن دیدگاه' : 'Write a comment';
?>
<!-- mb-comments-ui:v2 -->
<section class="mb-comments" id="comments" aria-labelledby="mb-comments-heading"
         data-mb-ui="v2"
         data-comments-url="<?php echo htmlspecialchars($urlpath, ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-success="<?php echo htmlspecialchars(i18n('comment_submission_success'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-moderation="<?php echo htmlspecialchars(i18n('comment_submission_moderation'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-error="<?php echo htmlspecialchars(i18n('comment_submission_error'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-shortname="<?php echo htmlspecialchars(i18n('comment_submission_error_shortname'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-email="<?php echo htmlspecialchars(i18n('comment_submission_error_email'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-short="<?php echo htmlspecialchars(i18n('comment_submission_error_short'), ENT_QUOTES, 'UTF-8'); ?>"
         data-msg-spam="<?php echo htmlspecialchars(i18n('comment_submission_error_spam'), ENT_QUOTES, 'UTF-8'); ?>">
    <header class="mb-comments__header">
        <div class="mb-comments__title-row">
            <h2 id="mb-comments-heading" class="mb-comments__title">
                <?php echo mb_icon('messages-square', 'mb-comments__title-icon'); ?>
                <?php echo i18n('Comments'); ?>
            </h2>
            <?php if ($count > 0): ?>
            <span class="mb-comments__count" aria-label="<?php echo htmlspecialchars($countLabel, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string)$count, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
        </div>
        <?php if ($count > 0): ?>
        <a class="mb-comments__jump" href="#mb-comment-composer"><?php echo htmlspecialchars($writeCta, ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endif; ?>
    </header>

    <div class="mb-comments__alert" id="mb-comment-alert" hidden role="status" aria-live="polite"></div>

    <?php if ($count > 0): ?>
    <ol class="mb-comments__list" aria-label="<?php echo htmlspecialchars(i18n('Comments'), ENT_QUOTES, 'UTF-8'); ?>">
        <?php foreach ($tree as $comment): ?>
            <?php echo mb_render_comment($comment, $urlpath); ?>
        <?php endforeach; ?>
    </ol>
    <?php else: ?>
    <div class="mb-comments__empty">
        <div class="mb-comments__empty-icon" aria-hidden="true">
            <?php echo mb_icon('message-circle', 'h-6 w-6'); ?>
        </div>
        <p class="mb-comments__empty-title"><?php echo htmlspecialchars($emptyTitle, ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="mb-comments__empty-hint"><?php echo htmlspecialchars($emptyHint, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php endif; ?>

    <div class="mb-comments__composer" id="mb-comment-composer">
        <h3 class="mb-comments__composer-title"><?php echo htmlspecialchars($composerTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
        <?php echo mb_comment_form($urlpath); ?>
    </div>

    <template id="mb-reply-form-template">
        <?php echo mb_comment_form('__URL__', '__PARENT__'); ?>
    </template>
</section>
