<?php
if (($totalPages ?? 1) <= 1) {
    return;
}
$page = $page ?? 1;
$paginationParams = $paginationParams ?? [];
$paginationUrl = function ($target) use ($paginationParams) {
    return '?' . http_build_query(array_merge($paginationParams, ['page' => $target]));
};
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a href="<?= e($paginationUrl($page - 1)) ?>" aria-label="Page précédente">‹</a>
    <?php else: ?>
        <span class="disabled" aria-hidden="true">‹</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i === $page): ?>
            <span class="current" aria-current="page"><?= $i ?></span>
        <?php else: ?>
            <a href="<?= e($paginationUrl($i)) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?= e($paginationUrl($page + 1)) ?>" aria-label="Page suivante">›</a>
    <?php else: ?>
        <span class="disabled" aria-hidden="true">›</span>
    <?php endif; ?>
</nav>
