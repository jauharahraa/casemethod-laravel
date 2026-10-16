{{-- Ilustrasi meja belajar (laptop, tanaman, cangkir). Dipakai di hero dashboard. --}}
<svg viewBox="0 0 460 360" class="mx-auto h-auto w-full max-w-[420px]" role="img" aria-label="Ilustrasi laptop, tanaman, dan cangkir">
  <defs><path id="leafd" d="M372 262 C360 226 358 190 372 148 C386 190 384 226 372 262Z"/></defs>
  <ellipse cx="235" cy="322" rx="205" ry="26" fill="#5f7cae" opacity=".12"/>
  <path d="M41 214v26a17 7 0 0 0 34 0v-26z" fill="#fff"/>
  <path d="M75 222c11 0 11 15 0 15" fill="none" stroke="#cfc8da" stroke-width="3" stroke-linecap="round"/>
  <ellipse cx="58" cy="214" rx="17" ry="7" fill="#fff" stroke="#e6e1ec"/><ellipse cx="58" cy="216" rx="13" ry="5" fill="#db88a4"/>
  <polygon points="93,250 214,320 214,328 93,258" fill="#cfc8da"/><polygon points="301,270 214,320 214,328 301,278" fill="#b9b1c8"/>
  <g transform="matrix(0.866 0.5 -0.866 0.5 180 200)">
    <rect x="0" y="0" width="140" height="100" rx="3" fill="#f3eff6"/>
    @for($r = 0; $r < 4; $r++) @for($c = 0; $c < 10; $c++)
      <rect x="{{ 12 + $c * 11.5 }}" y="{{ 10 + $r * 11 }}" width="8" height="8" rx="1.5" fill="#738fbd" opacity=".9"/>
    @endfor @endfor
    <rect x="48" y="66" width="44" height="22" rx="3" fill="#cc8eb1"/>
  </g>
  <polygon points="187,66 308,136 308,266 187,196" fill="#46608f"/>
  <g transform="matrix(0.866 0.5 0 1 180 70)">
    <rect x="0" y="0" width="140" height="130" rx="4" fill="#5f7cae"/><rect x="10" y="12" width="120" height="106" rx="2" fill="#fff"/>
    <rect x="18" y="20" width="52" height="7" rx="3" fill="#dbd6df"/><rect x="18" y="36" width="104" height="9" rx="4" fill="#eec6c7"/>
    <rect x="18" y="50" width="82" height="9" rx="4" fill="#a8c3d4"/><rect x="18" y="64" width="94" height="9" rx="4" fill="#cc8eb1"/>
    <rect x="18" y="82" width="44" height="28" rx="4" fill="#738fbd" opacity=".85"/><rect x="70" y="82" width="52" height="28" rx="4" fill="#dbd6df"/>
  </g>
  <use href="#leafd" fill="#8aa8c8" transform="rotate(-38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
  <use href="#leafd" fill="#8aa8c8" transform="rotate(38 372 262) translate(372 262) scale(.8) translate(-372 -262)"/>
  <use href="#leafd" fill="#738fbd" transform="rotate(-18 372 262)"/><use href="#leafd" fill="#a8c3d4" transform="rotate(18 372 262)"/>
  <use href="#leafd" fill="#5f7cae" transform="translate(372 262) scale(1.08) translate(-372 -262)"/>
  <path d="M346 262v44a26 12 0 0 0 52 0v-44z" fill="#fff" stroke="#e6e1ec"/><ellipse cx="372" cy="262" rx="26" ry="12" fill="#f4f0f6" stroke="#e6e1ec"/><ellipse cx="372" cy="262" rx="20" ry="8" fill="#b07a97"/>
  <g class="fl"><path d="M120 50l3 9 9 3-9 3-3 9-3-9-9-3 9-3z" fill="#db88a4"/><circle cx="410" cy="70" r="5" fill="#a8c3d4"/></g>
  <g class="fl2"><path d="M410 140l2.5 7 7 2.5-7 2.5-2.5 7-2.5-7-7-2.5 7-2.5z" fill="#cc8eb1"/><circle cx="60" cy="110" r="6" fill="#eec6c7"/></g>
</svg>