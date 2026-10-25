// Progressive enhancement: tudo funciona sem JS; com JS fica melhor.
// 1) Autocomplete de CEP via /api/cep (mesma origem, CSP-friendly)
// 2) Mapa Leaflet com origem/destino (se coords disponíveis)

(function () {
  'use strict';

  // ---------- CEP: máscara + autocomplete ----------
  document.querySelectorAll('input[data-cep]').forEach(function (campo) {
    var lado = campo.getAttribute('data-cep'); // "origem" | "destino"

    campo.addEventListener('input', function () {
      var v = campo.value.replace(/\D/g, '').slice(0, 8);
      campo.value = v.length > 5 ? v.slice(0, 5) + '-' + v.slice(5) : v;
    });

    campo.addEventListener('blur', function () {
      var cep = campo.value.replace(/\D/g, '');
      if (cep.length !== 8) return;

      fetch('/api/cep/' + cep)
        .then(function (r) {
          return r.ok ? r.json() : null;
        })
        .then(function (d) {
          if (!d) return;
          var pre = lado + '_';
          var set = function (sufixo, valor) {
            var alvo = document.querySelector('input[name="' + pre + sufixo + '"]');
            if (alvo && valor && !alvo.value) alvo.value = valor;
          };
          set('rua', d.rua);
          set('bairro', d.bairro);
          set('cidade', d.cidade);
          set('uf', d.uf);
        })
        .catch(function () {
          /* offline: usuário preenche na mão */
        });
    });
  });

  // ---------- Mapa (página de detalhe) ----------
  var mapaDiv = document.getElementById('mapa');
  if (mapaDiv && typeof L !== 'undefined') {
    var num = function (el, attr) {
      var v = parseFloat(el.getAttribute(attr));
      return isNaN(v) ? null : v;
    };
    var oLat = num(mapaDiv, 'data-origem-lat');
    var oLng = num(mapaDiv, 'data-origem-lng');
    var dLat = num(mapaDiv, 'data-destino-lat');
    var dLng = num(mapaDiv, 'data-destino-lng');

    if (oLat != null && oLng != null && dLat != null && dLng != null) {
      var mapa = L.map(mapaDiv).setView([(oLat + dLat) / 2, (oLng + dLng) / 2], 11);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap',
      }).addTo(mapa);

      var origem = L.marker([oLat, oLng]).addTo(mapa).bindPopup('Origem');
      var destino = L.marker([dLat, dLng]).addTo(mapa).bindPopup('Destino');
      L.polyline(
        [
          [oLat, oLng],
          [dLat, dLng],
        ],
        { color: '#2563eb', dashArray: '6 8' }
      ).addTo(mapa);

      var grupo = new L.featureGroup([origem, destino]);
      mapa.fitBounds(grupo.getBounds(), { padding: [24, 24] });
    } else {
      mapaDiv.style.display = 'none';
    }
  }
})();
