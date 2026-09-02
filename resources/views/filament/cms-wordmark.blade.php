<div class="nsi-cms-wordmark" aria-label="NSI Group">
    <span class="nsi-cms-wordmark__name">NSI</span>
    <span class="nsi-cms-wordmark__group">GROUP</span>
</div>

<style>
    .nsi-cms-wordmark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-width: 15rem;
        margin: 0 auto;
        font-weight: 700;
        line-height: 1;
        color: rgba(17, 24, 39, 0.72);
        text-transform: uppercase;
        letter-spacing: -0.06em;
    }

    .nsi-cms-wordmark__name {
        font-size: 2.2rem;
        color: rgba(17, 24, 39, 0.78);
    }

    .nsi-cms-wordmark__group {
        font-size: 0.78rem;
        letter-spacing: 0.42em;
        color: rgba(17, 24, 39, 0.6);
        transform: translateY(0.14rem);
    }

    @media (max-width: 640px) {
        .nsi-cms-wordmark {
            min-width: 0;
            gap: 0.25rem;
        }

        .nsi-cms-wordmark__name {
            font-size: 1.6rem;
        }

        .nsi-cms-wordmark__group {
            font-size: 0.58rem;
            letter-spacing: 0.28em;
        }
    }
</style>
