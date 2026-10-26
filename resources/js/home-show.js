/**
 * Home page "Show" — the cinematic storytelling slider in the video section.
 * Scenes (and all their text, for SEO) are rendered by Blade; this file only
 * enhances them: word/letter splitting, transitions, chapter bar, music.
 * Markup: resources/views/partials/home-show.blade.php · CSS: components/home-show.css
 */
(function () {
    var R = document.getElementById('home-show');
    if (!R) return;
    var E = [].slice.call(R.querySelectorAll('.hs-sc'));
    if (!E.length) return;

    var L = {};
    try { L = JSON.parse(R.getAttribute('data-l') || '{}'); } catch (x) {}
    var LOGO = R.getAttribute('data-logo') || '', TAG = R.getAttribute('data-tag') || '';
    var MUSIC = R.getAttribute('data-music') === '1';
    var FILE = R.getAttribute('data-file') || '';
    var lite = matchMedia('(prefers-reduced-motion: reduce)').matches
        || (navigator.connection && navigator.connection.saveData)
        || (navigator.deviceMemory && navigator.deviceMemory <= 2);
    if (lite) R.classList.add('hs-lite');

    var $ = function (q) { return R.querySelector(q); };
    var PUNCT = /^[,.;:!?)»…]+$/;

    function span(c, t, i) {
        var s = document.createElement('span');
        s.className = c;
        if (i != null) s.style.setProperty('--i', i);
        s.textContent = t;
        return s;
    }

    // Headline -> masked words (the <em class="hs-hl"> keyword stays one highlighted unit)
    function splitHead(h) {
        var n = 0, f = document.createDocumentFragment();
        h.setAttribute('aria-label', h.textContent.replace(/\s+/g, ' ').trim());
        [].slice.call(h.childNodes).forEach(function (nd) {
            var parts = nd.nodeType === 1
                ? [[nd.textContent, 1]]
                : nd.textContent.split(/(\s+)/).filter(Boolean).map(function (t) { return [t, 0]; });
            parts.forEach(function (p) {
                if (!p[1] && /^\s+$/.test(p[0])) { f.appendChild(document.createTextNode(' ')); return; }
                var i = PUNCT.test(p[0]) ? Math.max(n - 1, 0) : n++;
                var m = span('hs-m'), w = span('hs-w' + (p[1] ? ' hs-hl' : ''), p[0], i);
                w.setAttribute('aria-hidden', 'true');
                m.appendChild(w);
                f.appendChild(m);
            });
        });
        h.textContent = '';
        h.appendChild(f);
    }

    // Subline -> typewriter letters
    function splitSub(p) {
        var t = p.textContent.trim(), n = 0;
        p.setAttribute('aria-label', t);
        p.textContent = '';
        t.split(' ').forEach(function (w, k) {
            if (k) p.appendChild(document.createTextNode(' '));
            var s = span('hs-nw');
            Array.from(w).forEach(function (c) { s.appendChild(span('hs-ch', c, n++)); });
            p.appendChild(s);
        });
    }

    E.forEach(function (e) {
        var h = e.querySelector('h3');
        if (h) splitHead(h);
        var s = e.querySelector('.hs-sub[data-type]');
        if (s) splitSub(s);
    });

    // Controls
    R.insertAdjacentHTML('beforeend',
        '<div class="hs-top">' + (LOGO ? '<img class="hs-mkimg" src="' + LOGO + '" alt="NSI Group">' : '<span class="hs-mk"><b>NSI</b> GROUP</span>') + '<span class="hs-ctl">'
        + (MUSIC ? '<span class="hs-hint"></span><button type="button" class="hs-snd"></button>' : '')
        + '<span class="hs-ct"></span><button type="button" class="hs-pp"></button><button type="button" class="hs-rp">↻</button></span></div>'
        + '<nav class="hs-nav"></nav><div class="hs-intro"><div class="hs-ig"></div><div class="hs-ibox"><div class="hs-ilw">'
        + (LOGO ? '<img class="hs-ilogo" src="' + LOGO + '" alt="">' : '<b class="hs-itext">NSI GROUP</b>')
        + '</div><div class="hs-il"></div><p class="hs-itag"></p></div></div>');
    $('.hs-itag').textContent = TAG;
    var NAV = $('.hs-nav'), SW = $('.hs-sweep'), IN = $('.hs-intro'), CT = $('.hs-ct'),
        PP = $('.hs-pp'), SD = $('.hs-snd'), HI = $('.hs-hint');
    $('.hs-rp').setAttribute('aria-label', L.replay || 'Replay');
    $('.hs-nav').setAttribute('aria-label', L.chapters || 'Chapters');

    var SEG = E.map(function (e, k) {
        var g = document.createElement('button');
        g.type = 'button';
        g.className = 'hs-seg';
        g.style.setProperty('--d', (e.getAttribute('data-dur') || '6.5') + 's');
        g.innerHTML = '<i><b></b></i>';
        g.setAttribute('aria-label', e.getAttribute('data-chapter') || '');
        g.onclick = function () { go(k); };
        NAV.appendChild(g);
        return g;
    });

    var T = [];
    var cur = -1, busy = 0, P = 0, inside = 0, hd = document.hidden, vis = 0, booted = 0;

    function sync() {
        R.classList.toggle('hs-paused', !!(P || hd || !vis));
        aud();
    }
    function setPP() {
        PP.textContent = P ? '▶' : '❚❚';
        PP.setAttribute('aria-label', P ? (L.play || 'Play') : (L.pause || 'Pause'));
    }
    setPP();

    function show(n) {
        var a = E[cur], b = E[n];
        if (a) {
            a.classList.remove('hs-on');
            a.classList.add('hs-out');
            a.setAttribute('aria-hidden', 'true');
            setTimeout(function () { a.classList.remove('hs-out'); }, 1400);
        }
        b.classList.remove('hs-out');
        b.classList.add('hs-on');
        b.setAttribute('aria-hidden', 'false');
        SEG.forEach(function (g, j) {
            g.classList.toggle('hs-on', j === n);
            g.classList.toggle('hs-done', j < n);
        });
        CT.textContent = ('0' + (n + 1)).slice(-2) + ' / ' + ('0' + E.length).slice(-2);
        cur = n;
        if (snd && started) score(n);
    }

    function go(n) {
        n = (n + E.length) % E.length;
        if (n === cur || busy) return;
        if (!lite && E[n].getAttribute('data-t') === 'wipe' && cur >= 0) {
            busy = 1;
            SW.classList.remove('hs-go');
            void SW.offsetWidth;
            SW.classList.add('hs-go');
            if (snd && started) whoosh();
            setTimeout(function () { show(n); }, 520);
            setTimeout(function () { busy = 0; }, 1150);
        } else {
            show(n);
        }
    }

    // A segment's progress bar finishing = scene finished -> next scene
    NAV.addEventListener('animationend', function (e) {
        if (e.animationName === 'hs-fill' && e.target.parentNode.parentNode.classList.contains('hs-on')) go(cur + 1);
    });

    function play(intro) {
        cur = -1;
        busy = 0;
        E.forEach(function (e) { e.classList.remove('hs-on', 'hs-out'); });
        SEG.forEach(function (g) { g.classList.remove('hs-on', 'hs-done'); });
        T.forEach(clearTimeout);
        T = [];
        IN.classList.remove('hs-run', 'hs-out');
        if (intro) {
            void IN.offsetWidth;
            IN.classList.add('hs-run');
            T.push(setTimeout(function () { IN.classList.add('hs-out'); go(0); }, 2600));
            T.push(setTimeout(function () { IN.classList.remove('hs-run', 'hs-out'); }, 3800));
        } else {
            T.push(setTimeout(function () { go(0); }, 60));
        }
    }

    /* ---------- Music: optional uploaded track, otherwise a generated ambient score ---------- */
    var AC, MG, AU, snd = MUSIC, started = 0, gest = 0;
    var CH = [[45, 57, 60, 64], [41, 53, 57, 60], [36, 48, 52, 55], [43, 55, 59, 62]];
    var hz = function (m) { return 440 * Math.pow(2, (m - 69) / 12); };

    function tone(m, t, len, vol, type, cut, a) {
        var o = AC.createOscillator(), g = AC.createGain(), f = AC.createBiquadFilter();
        o.type = type;
        o.frequency.value = hz(m);
        o.detune.value = (Math.random() - .5) * 12;
        f.type = 'lowpass';
        f.frequency.value = cut;
        g.gain.setValueAtTime(0, t);
        g.gain.linearRampToValueAtTime(vol, t + len * a);
        g.gain.linearRampToValueAtTime(0, t + len);
        o.connect(f); f.connect(g); g.connect(MG);
        o.start(t); o.stop(t + len + .1);
    }
    function score(n) {
        if (AU) return;
        var t = AC.currentTime + .05, c = CH[n % 4], k;
        c.forEach(function (m, i) {
            tone(m, t, 8, i ? .05 : .1, i ? 'triangle' : 'sine', i ? 1100 : 400, .3);
            tone(m + .08, t, 8, .03, 'sawtooth', 700, .3);
        });
        for (k = 0; k < 8; k++) tone(c[1 + k % 3] + 12, t + 1 + k * .75, 1.6, .03, 'sine', 3000, .04);
    }
    function whoosh() {
        if (AU) return;
        var t = AC.currentTime, b = AC.createBuffer(1, AC.sampleRate, AC.sampleRate), d = b.getChannelData(0), i,
            s = AC.createBufferSource(), f = AC.createBiquadFilter(), g = AC.createGain();
        for (i = 0; i < d.length; i++) d[i] = Math.random() * 2 - 1;
        s.buffer = b;
        f.type = 'bandpass'; f.Q.value = 2;
        f.frequency.setValueAtTime(300, t);
        f.frequency.exponentialRampToValueAtTime(3500, t + 1);
        g.gain.setValueAtTime(0, t);
        g.gain.linearRampToValueAtTime(.12, t + .5);
        g.gain.linearRampToValueAtTime(0, t + 1);
        s.connect(f); f.connect(g); g.connect(MG);
        s.start(t);
    }
    function initAC() {
        if (AC) return;
        AC = new (window.AudioContext || window.webkitAudioContext)();
        MG = AC.createGain();
        MG.gain.value = 0;
        MG.connect(AC.destination);
        if (FILE) {
            AU = new Audio(FILE);
            AU.loop = true;
            AC.createMediaElementSource(AU).connect(MG);
        }
    }
    function fadeTo(v, d) {
        var t = AC.currentTime;
        MG.gain.cancelScheduledValues(t);
        MG.gain.setValueAtTime(MG.gain.value, t);
        MG.gain.linearRampToValueAtTime(v, t + d);
    }
    function ui() {
        if (!SD) return;
        var on = snd && started;
        SD.textContent = on ? '🔊' : '🔇';
        SD.setAttribute('aria-label', on ? (L.soundOff || 'Sound off') : (L.soundOn || 'Sound on'));
        HI.textContent = (snd && !started) ? (L.hint || '') : '';
    }
    // Keep sound only while the show is on screen, the tab is visible and it is not paused
    function aud() {
        if (!AC || !snd || !started) return;
        var active = vis && !hd && !P;
        if (active) {
            AC.resume();
            fadeTo(.55, 1.2);
        } else {
            fadeTo(0, .5);
            setTimeout(function () { if (!(vis && !hd && !P)) AC.suspend(); }, 650);
        }
    }
    function start() {
        try {
            initAC();
            AC.resume().then(function () {
                if (AC.state !== 'running' || started) return;
                started = 1;
                if (AU) AU.play();
                fadeTo(.55, 2.5);
                score(Math.max(cur, 0));
                ui();
            }).catch(function () { /* autoplay blocked — wait for the first click/tap */ });
        } catch (x) {}
    }
    // Sound is ON by default: try to start it as soon as the show is on screen.
    // Browsers that allow autoplay with sound (e.g. Chrome for visitors who often
    // play media on this site) start the music straight away. Browsers that block
    // it leave the AudioContext suspended — nothing plays, nothing breaks — and the
    // first click / tap / key anywhere on the page starts it instead (handlers below),
    // with the "Click for sound" hint shown until then.
    function maybeStart() { if (MUSIC && snd && !started && vis) start(); }
    ['pointerdown', 'keydown', 'touchend'].forEach(function (n) {
        addEventListener(n, function (e) {
            if (e.target && e.target.closest && e.target.closest('.hs-snd')) return;
            gest = 1;
            maybeStart();
        });
    });
    if (SD) {
        ui();
        SD.onclick = function () {
            try {
                initAC();
                if (snd && started) { snd = 0; fadeTo(0, .8); }
                else { snd = 1; started = 1; AC.resume(); if (AU) AU.play(); fadeTo(.55, 1.5); score(Math.max(cur, 0)); }
                ui();
            } catch (x) {}
        };
    }

    /* ---------- Controls & interaction ---------- */
    PP.onclick = function () { P = !P; setPP(); sync(); };
    $('.hs-rp').onclick = function () { play(true); };
    if (matchMedia('(hover:hover)').matches) {
        R.addEventListener('mouseenter', function () { inside = 1; });
        R.addEventListener('mouseleave', function () { inside = 0; });
    }
    document.addEventListener('visibilitychange', function () { hd = document.hidden; sync(); });
    if (!lite) {
        R.addEventListener('pointermove', function (e) {
            var r = R.getBoundingClientRect();
            R.style.setProperty('--mx', ((e.clientX - r.left) / r.width - .5).toFixed(3));
            R.style.setProperty('--my', ((e.clientY - r.top) / r.height - .5).toFixed(3));
        });
    }
    var sx = 0;
    R.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    R.addEventListener('touchend', function (e) {
        var d = e.changedTouches[0].clientX - sx;
        if (Math.abs(d) > 50) go(cur + (d < 0 ? 1 : -1));
    });
    addEventListener('keydown', function (e) {
        if (!inside) return;
        if (e.key === 'ArrowRight') go(cur + 1);
        else if (e.key === 'ArrowLeft') go(cur - 1);
        else if (e.key === ' ') { e.preventDefault(); PP.click(); }
    });

    /* ---------- Start when the show scrolls into view; pause when it leaves ---------- */
    var seen = 0;
    try { seen = sessionStorage.getItem('nsiShowIntro'); } catch (x) {}
    function onVis(v) {
        vis = v;
        if (v && !booted) {
            booted = 1;
            play(!seen && !lite);
            try { sessionStorage.setItem('nsiShowIntro', '1'); } catch (x) {}
        }
        sync();
        maybeStart();
    }
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (en) { onVis(en[0].isIntersecting); }, { threshold: .35 }).observe(R);
    } else {
        onVis(true);
    }
})();