<div class="nsi-brand-logo" aria-label="NSI Group">
    <div class="nsi-brand-logo__mark" aria-hidden="true">
        <span class="nsi-brand-logo__slice"></span>
        <span class="nsi-brand-logo__dot"></span>
    </div>

    <div class="nsi-brand-logo__wordmark" aria-hidden="true">
        <span class="nsi-brand-logo__name">NSI</span>
        <span class="nsi-brand-logo__group">GROUP</span>
    </div>
</div>

<style>
    .nsi-brand-logo {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        line-height: 1;
        vertical-align: middle;
    }

    .nsi-brand-logo__mark {
        position: relative;
        width: 3.3rem;
        height: 3.3rem;
        flex-shrink: 0;
        border-radius: 50%;
        background: linear-gradient(135deg, #e5e7eb 0%, #a3a7ae 46%, #7a7d83 100%);
        box-shadow: inset 0 -0.25rem 0.5rem rgba(17, 24, 39, 0.15);
        overflow: hidden;
    }

    .nsi-brand-logo__slice {
        position: absolute;
        inset: 0.15rem -0.3rem 0.15rem 0.25rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #1d5fae 0%, #0f47a5 38%, #0a3f92 100%);
        transform: rotate(-28deg) skewX(-8deg);
        box-shadow: inset -0.3rem 0 0 rgba(255,255,255,0.14);
    }

    .nsi-brand-logo__slice::after {
        content: "";
        position: absolute;
        inset: 0.2rem 0.28rem 0.2rem 0.42rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255,255,255,0.38), rgba(255,255,255,0.04));
        transform: rotate(12deg);
    }

    .nsi-brand-logo__dot {
        position: absolute;
        right: 0.38rem;
        top: 0.38rem;
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #f9b94b 0%, #f5a623 100%);
        box-shadow: 0 0 0 0.14rem rgba(255,255,255,0.3);
    }

    .nsi-brand-logo__wordmark {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.12rem;
        color: #dfe3e8;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        filter: drop-shadow(0 0.12rem 0 rgba(18,21,28,0.18));
    }

    .nsi-brand-logo__name {
        font-size: 2.7rem;
        font-weight: 700;
        letter-spacing: -0.08em;
        line-height: 0.8;
        color: rgba(255,255,255,0.86);
    }

    .nsi-brand-logo__group {
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.42em;
        color: rgba(255,255,255,0.76);
        margin-left: 0.18rem;
    }

    @media (max-width: 640px) {
        .nsi-brand-logo__mark {
            width: 2.3rem;
            height: 2.3rem;
        }

        .nsi-brand-logo__name {
            font-size: 2rem;
        }

        .nsi-brand-logo__group {
            letter-spacing: 0.28em;
            font-size: 0.72rem;
        }
    }
</style>
</div>
