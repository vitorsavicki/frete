// Quero Mudar Já — progressive enhancement (tudo funciona sem JS).
// Menu mobile, autocomplete de CEP, mapas, estimador de custo, máscaras e IA.

(function () {
  'use strict';

  var $ = function (seletor, contexto) {
    return (contexto || document).querySelector(seletor);
  };
  var $$ = function (seletor, contexto) {
    return Array.prototype.slice.call((contexto || document).querySelectorAll(seletor));
  };

  var fmtMoeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
  var fmtNumero = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

  // Atribuição exigida pela licença ODbL e pela política de tiles do OSM.
  var ATRIBUICAO_OSM =
    '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors' +
    ' · <a href="https://www.openstreetmap.org/fixthemap" target="_blank" rel="noopener">Reportar problema no mapa</a>';

  // ---------- menu mobile / cabeçalho ----------
  var body = document.body;
  var header = $('[data-header]');
  var toggle = $('[data-nav-toggle]');
  var nav = $('[data-nav]');

  function fecharMenu() {
    body.classList.remove('nav-aberto');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  }

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var aberto = body.classList.toggle('nav-aberto');
      toggle.setAttribute('aria-expanded', aberto ? 'true' : 'false');
    });

    $$('a', nav).forEach(function (link) {
      link.addEventListener('click', fecharMenu);
    });

    document.addEventListener('keydown', function (evento) {
      if (evento.key === 'Escape' && body.classList.contains('nav-aberto')) {
        fecharMenu();
        toggle.focus();
      }
    });

    document.addEventListener('click', function (evento) {
      if (!body.classList.contains('nav-aberto')) return;
      if (header && header.contains(evento.target)) return;
      fecharMenu();
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 900) fecharMenu();
    });
  }

  if (header) {
    var sombra = function () {
      header.classList.toggle('tem-sombra', window.scrollY > 4);
    };
    window.addEventListener('scroll', sombra, { passive: true });
    sombra();
  }

  // ---------- cadastro: papel, senha e documento ----------
  function atualizarPapel() {
    var selecionado = $('[data-papel]:checked');
    if (!selecionado) return;
    var papel = selecionado.value === 'transportador' ? 'transportador' : 'cliente';

    $$('[data-papel]').forEach(function (radio) {
      var caixa = radio.closest('.opcao');
      if (caixa) caixa.classList.toggle('ativa', radio.checked);
    });

    var google = $('[data-google]');
    if (google) google.setAttribute('href', '/auth/google?papel=' + papel);

    var nota = $('[data-google-nota]');
    if (nota) {
      nota.textContent =
        'A conta será criada como ' + (papel === 'transportador' ? 'transportador' : 'cliente') + '.';
    }

    var dica = $('[data-doc-dica]');
    if (dica) dica.textContent = papel === 'transportador' ? '(obrigatório)' : '(opcional)';

    var documento = $('input[name="cpf_cnpj"]');
    if (documento) {
      documento.required = papel === 'transportador';
      documento.placeholder =
        papel === 'transportador' ? 'CPF ou CNPJ (obrigatório)' : '000.000.000-00';
    }
  }

  var papelSelector = $('[data-papel-selector]');
  if (papelSelector) {
    $$('[data-papel]', papelSelector).forEach(function (radio) {
      radio.addEventListener('change', atualizarPapel);
    });
    atualizarPapel();
  }

  $$('[data-ver-senha]').forEach(function (botao) {
    botao.addEventListener('click', function () {
      var caixa = botao.closest('.campo-senha');
      var campo = caixa ? $('input', caixa) : null;
      if (!campo) return;
      var mostrar = campo.type === 'password';
      campo.type = mostrar ? 'text' : 'password';
      botao.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
      botao.textContent = mostrar ? '🙈' : '👁️';
      campo.focus();
    });
  });

  var senha = $('[data-senha]');
  var senha2 = $('[data-senha2]');
  var forca = $('[data-forca]');
  var forcaTexto = $('[data-forca-texto]');
  var senhaConfere = $('[data-senha-confere]');

  function nivelSenha(valor) {
    if (!valor) return 0;
    var nivel = 0;
    if (valor.length >= 8) nivel++;
    if (/[a-z]/.test(valor) && /[A-Z]/.test(valor)) nivel++;
    if (/\d/.test(valor)) nivel++;
    if (/[^A-Za-z0-9]/.test(valor)) nivel++;
    if (valor.length >= 12 && nivel < 4) nivel++;
    return Math.min(4, nivel);
  }

  function atualizarForca() {
    if (!senha || !forca) return;
    var nivel = nivelSenha(senha.value);
    forca.setAttribute('data-nivel', String(nivel));
    if (forcaTexto) {
      var mensagens = [
        'Use 8+ caracteres, com letras e números.',
        'Senha fraca — adicione mais caracteres.',
        'Senha razoável — misture maiúsculas e números.',
        'Senha boa — inclua um símbolo para melhorar.',
        'Senha forte. Bom trabalho!',
      ];
      forcaTexto.textContent = mensagens[nivel];
      forcaTexto.classList.toggle('campo__dica--ok', nivel >= 3);
      forcaTexto.classList.toggle('campo__dica--erro', nivel === 1);
    }
    atualizarConfere();
  }

  function atualizarConfere() {
    if (!senha2 || !senhaConfere || !senha) return;
    if (!senha2.value) {
      senhaConfere.textContent = 'As duas senhas precisam ser iguais.';
      senhaConfere.classList.remove('campo__dica--ok', 'campo__dica--erro');
      senha2.setCustomValidity('');
      return;
    }
    var igual = senha.value === senha2.value;
    senhaConfere.textContent = igual ? 'As senhas conferem.' : 'As senhas ainda não conferem.';
    senhaConfere.classList.toggle('campo__dica--ok', igual);
    senhaConfere.classList.toggle('campo__dica--erro', !igual);
    senha2.setCustomValidity(igual ? '' : 'As senhas não conferem');
  }

  if (senha) {
    senha.addEventListener('input', atualizarForca);
    atualizarForca();
  }
  if (senha2) {
    senha2.addEventListener('input', atualizarConfere);
  }

  // ---------- máscaras ----------
  function somenteDigitos(valor) {
    return String(valor || '').replace(/\D/g, '');
  }

  $$('input[data-telefone]').forEach(function (campo) {
    campo.addEventListener('input', function () {
      var d = somenteDigitos(campo.value).slice(0, 11);
      if (!d) {
        campo.value = '';
      } else if (d.length <= 2) {
        campo.value = '(' + d;
      } else if (d.length <= 6) {
        campo.value = '(' + d.slice(0, 2) + ') ' + d.slice(2);
      } else if (d.length <= 10) {
        campo.value = '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
      } else {
        campo.value = '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
      }
    });
  });

  $$('input[data-documento]').forEach(function (campo) {
    campo.addEventListener('input', function () {
      var d = somenteDigitos(campo.value).slice(0, 14);
      if (!d) {
        campo.value = '';
      } else if (d.length <= 11) {
        var cpf = d;
        if (cpf.length > 9) cpf = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6, 9) + '-' + cpf.slice(9);
        else if (cpf.length > 6) cpf = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6);
        else if (cpf.length > 3) cpf = cpf.slice(0, 3) + '.' + cpf.slice(3);
        campo.value = cpf;
      } else {
        campo.value =
          d.slice(0, 2) + '.' + d.slice(2, 5) + '.' + d.slice(5, 8) + '/' + d.slice(8, 12) + '-' + d.slice(12);
      }
    });
  });

  $$('input[data-uf]').forEach(function (campo) {
    campo.addEventListener('input', function () {
      campo.value = campo.value.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 2);
    });
  });

  function formatarMoedaCampo(campo) {
    var bruto = String(campo.value || '').trim().replace(/[^\d.,]/g, '');
    if (!bruto) {
      campo.value = '';
      return;
    }

    var numero;
    if (bruto.indexOf(',') >= 0) {
      var partes = bruto.split(',');
      var inteiro = partes.shift().replace(/\./g, '');
      var decimais = partes.join('').replace(/\D/g, '').slice(0, 2);
      numero = Number((inteiro || '0') + '.' + (decimais || '0'));
    } else if (/^\d{1,3}(\.\d{3})+$/.test(bruto)) {
      numero = Number(bruto.replace(/\./g, ''));
    } else {
      numero = Number(bruto.replace(/,/g, '.'));
    }

    if (!isFinite(numero) || numero <= 0) {
      campo.value = '';
      return;
    }
    campo.value = numero.toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  $$('input[data-moeda]').forEach(function (campo) {
    campo.addEventListener('blur', function () {
      formatarMoedaCampo(campo);
    });
  });

  $$('form[data-confirm]').forEach(function (formulario) {
    formulario.addEventListener('submit', function (evento) {
      var mensagem = formulario.getAttribute('data-confirm');
      if (mensagem && !window.confirm(mensagem)) evento.preventDefault();
    });
  });

  // ---------- CEP + mapa da publicação ----------
  var coordsForm = { origem: null, destino: null };
  var mapaFormEl = $('[data-mapa-form]');
  var mapaForm = null;
  var camadaPontosForm = null;
  var camadaRotaForm = null;
  var rotasForm = {};
  var sequenciaRotaForm = 0;
  var statusMapaForm = $('[data-mapa-form-status]');

  function configurarIconesLeaflet() {
    if (typeof L === 'undefined' || !L.Icon || !L.Icon.Default) return;
    // O Leaflet prefixa os nomes relativos com imagePath. Informar caminhos
    // absolutos em iconUrl duplicaria a pasta (/images//vendor/...).
    L.Icon.Default.imagePath = '/vendor/leaflet/images/';
  }

  function haversine(lat1, lng1, lat2, lng2) {
    var R = 6371;
    var rad = Math.PI / 180;
    var dLat = (lat2 - lat1) * rad;
    var dLng = (lng2 - lng1) * rad;
    var a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * rad) * Math.cos(lat2 * rad) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function coordenadaValida(lat, lng) {
    return isFinite(lat) && isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180;
  }

  function chaveRotaForm(origem, destino) {
    return (
      origem.lat.toFixed(4) +
      ',' +
      origem.lng.toFixed(4) +
      ';' +
      destino.lat.toFixed(4) +
      ',' +
      destino.lng.toFixed(4)
    );
  }

  function tempoDeRota(minutos) {
    if (!isFinite(minutos) || minutos <= 0) return '';
    var horas = Math.floor(minutos / 60);
    var resto = Math.round(minutos % 60);
    if (horas && resto) return horas + ' h ' + resto + ' min';
    if (horas) return horas + ' h';
    return resto + ' min';
  }

  function geometriaDaRota(rota) {
    if (!rota || !rota.geometria || !rota.geometria.length) return [];
    var pontos = [];
    for (var i = 0; i < rota.geometria.length; i++) {
      var ponto = rota.geometria[i];
      if (ponto && ponto.length >= 2) {
        var lat = Number(ponto[0]);
        var lng = Number(ponto[1]);
        if (coordenadaValida(lat, lng)) pontos.push([lat, lng]);
      }
    }
    return pontos.length >= 2 ? pontos : [];
  }

  function desenharRotaForm(rota, aproximada) {
    if (!camadaRotaForm) return;
    camadaRotaForm.clearLayers();
    var geometria = geometriaDaRota(rota);
    if (geometria.length < 2) return;

    var real = rota.fonte === 'osrm' && !aproximada;
    var opcoes = {
      color: real ? '#1d4ed8' : '#64748b',
      weight: real ? 4 : 3,
      opacity: real ? 0.9 : 0.75,
    };
    if (!real) opcoes.dashArray = '7 8';
    L.polyline(geometria, opcoes).addTo(camadaRotaForm);

    if (!statusMapaForm) return;
    var distancia = Number(rota.distanciaKm);
    if (!isFinite(distancia) || distancia <= 0) distancia = 0;
    var km = fmtNumero.format(Math.round(distancia));
    if (real) {
      var tempo = tempoDeRota(Number(rota.duracaoMin));
      statusMapaForm.textContent =
        'Rota rodoviária: ' +
        km +
        ' km' +
        (tempo ? ' · ' + tempo : '') +
        '. Traçado real da estrada.';
    } else {
      statusMapaForm.textContent =
        'Prévia aproximada: ' + km + ' km por estrada (linha reta × 1,25).';
    }
  }

  function buscarRotaForm(origem, destino) {
    var chave = chaveRotaForm(origem, destino);
    var salva = rotasForm[chave];
    if (salva) {
      desenharRotaForm(salva, false);
      return;
    }

    // Cada pedido novo invalida o anterior: se o usuário trocar um CEP antes da
    // resposta chegar, o traçado antigo não sobrescreve o novo.
    sequenciaRotaForm += 1;
    var sequencia = sequenciaRotaForm;
    if (statusMapaForm) statusMapaForm.textContent = 'Calculando a rota rodoviária…';

    var params = new URLSearchParams({
      origem_lat: String(origem.lat),
      origem_lng: String(origem.lng),
      destino_lat: String(destino.lat),
      destino_lng: String(destino.lng),
    });

    fetch('/api/rota?' + params.toString(), { headers: { Accept: 'application/json' } })
      .then(function (resposta) {
        if (!resposta.ok) throw new Error('rota');
        return resposta.json();
      })
      .then(function (rota) {
        if (sequencia !== sequenciaRotaForm) return;
        if (geometriaDaRota(rota).length < 2) throw new Error('rota');
        rotasForm[chave] = rota;
        desenharRotaForm(rota, false);
      })
      .catch(function () {
        if (sequencia !== sequenciaRotaForm) return;
        // Fallback local: mesmo traçado e mesmo fator usados pelo servidor
        // quando o OSRM não responde. O aviso deixa claro que é aproximado.
        var linhaReta = haversine(origem.lat, origem.lng, destino.lat, destino.lng);
        desenharRotaForm(
          {
            distanciaKm: Math.round(linhaReta * 1.25 * 10) / 10,
            duracaoMin: null,
            geometria: [
              [origem.lat, origem.lng],
              [destino.lat, destino.lng],
            ],
            fonte: 'aproximada',
          },
          true
        );
      });
  }

  function atualizarMapaForm() {
    if (!mapaFormEl) return;
    var origem = coordsForm.origem;
    var destino = coordsForm.destino;
    if (!origem && !destino) return;
    if (typeof L === 'undefined') {
      if (statusMapaForm) statusMapaForm.textContent = 'Não foi possível carregar o mapa agora.';
      return;
    }

    configurarIconesLeaflet();

    if (!mapaForm) {
      mapaFormEl.innerHTML = '';
      mapaForm = L.map(mapaFormEl, { scrollWheelZoom: false }).setView([-15.78, -47.93], 4);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ATRIBUICAO_OSM,
      }).addTo(mapaForm);
      camadaPontosForm = L.layerGroup().addTo(mapaForm);
      camadaRotaForm = L.layerGroup().addTo(mapaForm);
    }

    camadaPontosForm.clearLayers();
    camadaRotaForm.clearLayers();
    sequenciaRotaForm += 1;
    var pontos = [];

    if (origem) {
      L.marker([origem.lat, origem.lng]).addTo(camadaPontosForm).bindPopup('Origem');
      pontos.push([origem.lat, origem.lng]);
    }
    if (destino) {
      L.marker([destino.lat, destino.lng]).addTo(camadaPontosForm).bindPopup('Destino');
      pontos.push([destino.lat, destino.lng]);
    }
    if (origem && destino) {
      mapaForm.fitBounds(L.latLngBounds(pontos), { padding: [30, 30], maxZoom: 12 });
      // Prévia imediata em linha reta enquanto a rota real é calculada. Assim o
      // mapa nunca fica vazio esperando o OSRM.
      L.polyline(
        [
          [origem.lat, origem.lng],
          [destino.lat, destino.lng],
        ],
        { color: '#64748b', weight: 3, dashArray: '7 8', opacity: 0.75 }
      ).addTo(camadaRotaForm);
      buscarRotaForm(origem, destino);
    } else {
      mapaForm.setView(pontos[0], 11);
      if (statusMapaForm) {
        statusMapaForm.textContent = 'Informe o outro CEP para completar a rota no mapa.';
      }
    }

    setTimeout(function () {
      if (mapaForm) mapaForm.invalidateSize();
    }, 80);
  }

  function statusCep(lado, texto, erro) {
    var campo = $('input[data-cep="' + lado + '"]');
    if (!campo) return;
    var alvo = $('[data-cep-status="' + lado + '"]');
    if (!alvo) {
      alvo = document.createElement('span');
      alvo.setAttribute('data-cep-status', lado);
      alvo.className = 'campo__dica';
      alvo.setAttribute('aria-live', 'polite');
      if (campo.parentNode) campo.parentNode.appendChild(alvo);
    }
    alvo.textContent = texto;
    alvo.classList.toggle('campo__dica--erro', Boolean(erro));
    alvo.classList.toggle('campo__dica--ok', !erro && Boolean(texto));
  }

  function preencherEndereco(lado, dados) {
    var valores = {
      rua: dados.rua || '',
      bairro: dados.bairro || '',
      cidade: dados.cidade || '',
      uf: dados.uf || '',
    };
    Object.keys(valores).forEach(function (sufixo) {
      var campo = $('input[name="' + lado + '_' + sufixo + '"]');
      if (campo && valores[sufixo]) campo.value = valores[sufixo];
    });
  }

  function consultarCep(campo) {
    var cep = somenteDigitos(campo.value);
    if (cep.length !== 8) return;
    if (campo.getAttribute('data-consultado') === cep) return;

    var lado = campo.getAttribute('data-cep');
    campo.setAttribute('data-consultado', cep);
    campo.setAttribute('aria-busy', 'true');
    statusCep(lado, 'Buscando endereço…', false);

    fetch('/api/cep/' + cep, { headers: { Accept: 'application/json' } })
      .then(function (resposta) {
        if (!resposta.ok) throw new Error('cep');
        return resposta.json();
      })
      .then(function (dados) {
        preencherEndereco(lado, dados);
        var lat = Number(dados.lat);
        var lng = Number(dados.lng);
        coordsForm[lado] = coordenadaValida(lat, lng) ? { lat: lat, lng: lng } : null;
        if (coordsForm[lado]) {
          statusCep(lado, dados.geocodificado ? 'Endereço preenchido (localização aproximada).' : 'Endereço preenchido.', false);
        } else {
          statusCep(lado, 'Endereço preenchido. Não foi possível localizar no mapa.', false);
        }
        atualizarMapaForm();
      })
      .catch(function () {
        coordsForm[lado] = null;
        campo.removeAttribute('data-consultado');
        statusCep(lado, 'CEP não encontrado. Confira os números ou preencha manualmente.', true);
        atualizarMapaForm();
      })
      .finally(function () {
        campo.removeAttribute('aria-busy');
      });
  }

  $$('input[data-cep]').forEach(function (campo) {
    var temporizador = null;

    campo.addEventListener('input', function () {
      var d = somenteDigitos(campo.value).slice(0, 8);
      campo.value = d.length > 5 ? d.slice(0, 5) + '-' + d.slice(5) : d;
      campo.removeAttribute('data-consultado');
      if (temporizador) window.clearTimeout(temporizador);
      if (d.length === 8) {
        temporizador = window.setTimeout(function () {
          consultarCep(campo);
        }, 500);
      }
    });

    campo.addEventListener('blur', function () {
      if (temporizador) window.clearTimeout(temporizador);
      consultarCep(campo);
    });

    if (somenteDigitos(campo.value).length === 8) {
      window.setTimeout(function () {
        consultarCep(campo);
      }, 120);
    }
  });

  // ---------- mapa do detalhe ----------
  var mapaDetalhe = $('[data-mapa]');
  if (mapaDetalhe && typeof L !== 'undefined') {
    var oLat = Number(mapaDetalhe.getAttribute('data-origem-lat'));
    var oLng = Number(mapaDetalhe.getAttribute('data-origem-lng'));
    var dLat = Number(mapaDetalhe.getAttribute('data-destino-lat'));
    var dLng = Number(mapaDetalhe.getAttribute('data-destino-lng'));
    var temOrigem = mapaDetalhe.getAttribute('data-origem-lat') && coordenadaValida(oLat, oLng);
    var temDestino = mapaDetalhe.getAttribute('data-destino-lat') && coordenadaValida(dLat, dLng);

    if (!temOrigem && !temDestino) {
      var vazio = $('.mapa__vazio', mapaDetalhe);
      if (vazio) {
        vazio.textContent =
          'Ainda não conseguimos localizar este endereço no mapa. Use os endereços acima para se orientar.';
      }
    } else if (!mapaDetalhe.getAttribute('data-iniciado')) {
      configurarIconesLeaflet();
      mapaDetalhe.setAttribute('data-iniciado', '1');
      mapaDetalhe.innerHTML = '';

      var centro = temOrigem ? [oLat, oLng] : [dLat, dLng];
      var mapa = L.map(mapaDetalhe, { scrollWheelZoom: false }).setView(centro, 11);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: ATRIBUICAO_OSM,
      }).addTo(mapa);

      var pontosMapa = [];
      var grupo = L.featureGroup().addTo(mapa);

      if (temOrigem) {
        L.marker([oLat, oLng]).addTo(grupo).bindPopup('Origem');
        pontosMapa.push([oLat, oLng]);
      }
      if (temDestino) {
        L.marker([dLat, dLng]).addTo(grupo).bindPopup('Destino');
        pontosMapa.push([dLat, dLng]);
      }
      if (temOrigem && temDestino) {
        L.polyline(
          [
            [oLat, oLng],
            [dLat, dLng],
          ],
          { color: '#1d4ed8', weight: 3, dashArray: '7 8', opacity: 0.85 }
        ).addTo(grupo);
        mapa.fitBounds(grupo.getBounds(), { padding: [28, 28], maxZoom: 12 });
      } else {
        mapa.setView(pontosMapa[0], 12);
      }

      setTimeout(function () {
        mapa.invalidateSize();
      }, 80);
    }
  }

  // ---------- perfil do caminhão: volume e medidas típicas ----------
  var formCaminhao = $('[data-form-caminhao]');
  if (formCaminhao) {
    var veiculosCaminhao = [];
    try {
      veiculosCaminhao = JSON.parse(formCaminhao.getAttribute('data-veiculos') || '[]') || [];
    } catch (e) {
      veiculosCaminhao = [];
    }

    var saidaVolume = $('[data-veiculo-volume]', formCaminhao);
    var dicaVolume = $('[data-veiculo-dica]', formCaminhao);
    var fmtPesoCaminhao = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });

    function metrosDoCampo(nome) {
      var campo = $('input[name="' + nome + '"]', formCaminhao);
      if (!campo) return 0;
      var n = Number(String(campo.value).replace(',', '.'));
      return isFinite(n) && n > 0 ? n : 0;
    }

    function atualizarVolumeCaminhao() {
      if (!saidaVolume) return;
      var volume = metrosDoCampo('comprimento') * metrosDoCampo('largura') * metrosDoCampo('altura');
      if (volume > 0) {
        saidaVolume.textContent = fmtNumero.format(Math.round(volume * 10) / 10) + ' m³';
        if (dicaVolume) dicaVolume.textContent = 'Volume interno calculado com as medidas acima.';
      } else {
        saidaVolume.textContent = '—';
        if (dicaVolume) dicaVolume.textContent = 'Preencha as três medidas para calcular o volume interno.';
      }
    }

    $$('[data-veiculo-medida]', formCaminhao).forEach(function (campo) {
      campo.addEventListener('input', atualizarVolumeCaminhao);
    });

    var botaoPadrao = $('[data-veiculo-padrao]', formCaminhao);
    if (botaoPadrao) {
      botaoPadrao.addEventListener('click', function () {
        var select = $('[data-veiculo-tipo]', formCaminhao);
        var tipo = select ? select.value : '';
        var veiculo = null;
        for (var i = 0; i < veiculosCaminhao.length; i++) {
          if (veiculosCaminhao[i].id === tipo) {
            veiculo = veiculosCaminhao[i];
            break;
          }
        }
        if (!veiculo) {
          if (dicaVolume) {
            dicaVolume.textContent = 'Escolha um tipo de caminhão na lista para usar as medidas típicas.';
          }
          return;
        }

        var medidas = {
          comprimento: veiculo.comprimentoCm / 100,
          largura: veiculo.larguraCm / 100,
          altura: veiculo.alturaCm / 100,
        };
        Object.keys(medidas).forEach(function (nome) {
          var campo = $('input[name="' + nome + '"]', formCaminhao);
          if (campo) campo.value = String(Math.round(medidas[nome] * 100) / 100);
        });
        var capacidade = $('[data-veiculo-capacidade]', formCaminhao);
        if (capacidade) capacidade.value = String(veiculo.capacidadeKg);
        atualizarVolumeCaminhao();
        if (dicaVolume) {
          dicaVolume.textContent =
            'Medidas típicas de ' +
            veiculo.nome +
            ' aplicadas (até ' +
            fmtPesoCaminhao.format(veiculo.capacidadeKg) +
            ' kg). Confira as medidas internas do seu veículo.';
        }
      });
    }

    atualizarVolumeCaminhao();

    // ---------- combustível: diesel ou gasolina ----------
    var referenciasCaminhao = {};
    var taxasCaminhao = {};
    try {
      referenciasCaminhao = JSON.parse(formCaminhao.getAttribute('data-referencias') || '{}') || {};
    } catch (e) {
      referenciasCaminhao = {};
    }
    try {
      taxasCaminhao = JSON.parse(formCaminhao.getAttribute('data-taxas') || '{}') || {};
    } catch (e) {
      taxasCaminhao = {};
    }

    // Formato novo: { combustivel: {...}, taxaBasePorTipo: {...} }.
    // Formato antigo: { diesel, gasolina }. Os dois continuam aceitos.
    var precosCombustivel = referenciasCaminhao.combustivel || referenciasCaminhao;
    if (!taxasCaminhao || Object.keys(taxasCaminhao).length === 0) {
      taxasCaminhao = referenciasCaminhao.taxaBasePorTipo || {};
    }

    var seletorCombustivel = $('[data-combustivel-tipo]', formCaminhao);
    var campoPreco = $('[data-combustivel-preco]', formCaminhao);

    function combustivelDoPerfil() {
      return seletorCombustivel && seletorCombustivel.value === 'gasolina' ? 'gasolina' : 'diesel';
    }

    function atualizarCombustivelPerfil() {
      var tipo = combustivelDoPerfil();
      var textoReferencia = $('[data-combustivel-referencia-texto]', formCaminhao);
      if (textoReferencia && precosCombustivel[tipo] != null) {
        textoReferencia.textContent = String(precosCombustivel[tipo]);
      }
    }

    if (seletorCombustivel) {
      seletorCombustivel.addEventListener('change', atualizarCombustivelPerfil);
    }

    var botaoReferenciaCombustivel = $('[data-combustivel-referencia]', formCaminhao);
    if (botaoReferenciaCombustivel && campoPreco) {
      botaoReferenciaCombustivel.addEventListener('click', function () {
        var tipo = combustivelDoPerfil();
        if (precosCombustivel[tipo] != null) {
          campoPreco.value = String(precosCombustivel[tipo]);
          campoPreco.dispatchEvent(new Event('input', { bubbles: true }));
        }
      });
    }

    var seletorTipoCaminhao = $('[data-veiculo-tipo]', formCaminhao);
    function atualizarTaxaSugerida() {
      if (!seletorTipoCaminhao) return;
      var tipo = seletorTipoCaminhao.value;
      var taxa = taxasCaminhao[tipo];
      var dica = $('[data-taxa-sugestao]', formCaminhao);
      var botao = $('[data-taxa-referencia]', formCaminhao);
      if (dica && taxa != null) {
        var opcao = seletorTipoCaminhao.options[seletorTipoCaminhao.selectedIndex];
        var rotulo = opcao ? opcao.textContent.toLowerCase() : tipo;
        dica.textContent = 'Sugestão para ' + rotulo + ': ' + fmtMoeda.format(taxa) + '. ';
        if (botao) {
          dica.appendChild(botao);
          botao.hidden = false;
        }
      }
    }

    if (seletorTipoCaminhao) {
      seletorTipoCaminhao.addEventListener('change', function () {
        atualizarTaxaSugerida();
        // A sugestão acompanha o veículo: fiorino tende a gasolina; pesados,
        // diesel. O transportador continua livre para trocar depois.
        if (seletorCombustivel) {
          var sugerido = seletorTipoCaminhao.value === 'fiorino' ? 'gasolina' : 'diesel';
          seletorCombustivel.value = sugerido;
          if (campoPreco && precosCombustivel[sugerido] != null) {
            campoPreco.value = String(precosCombustivel[sugerido]);
          }
          atualizarCombustivelPerfil();
        }
      });
    }

    var botaoTaxa = $('[data-taxa-referencia]', formCaminhao);
    if (botaoTaxa) {
      botaoTaxa.addEventListener('click', function () {
        var tipo = seletorTipoCaminhao ? seletorTipoCaminhao.value : '';
        var taxa = taxasCaminhao[tipo];
        var campoTaxa = $('input[name="taxa_base"]', formCaminhao);
        if (taxa != null && campoTaxa) {
          campoTaxa.value = String(taxa);
          campoTaxa.dispatchEvent(new Event('input', { bubbles: true }));
        }
      });
    }

    atualizarCombustivelPerfil();
    atualizarTaxaSugerida();
  }

  // ---------- montador de carga (opcional no cadastro do frete) ----------
  var montador = $('[data-montador-carga]');
  if (montador) {
    var veiculosCarga = [];
    try {
      veiculosCarga = JSON.parse(montador.getAttribute('data-veiculos') || '[]') || [];
    } catch (e) {
      veiculosCarga = [];
    }

    var fmtPesoCarga = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 });
    var TAMANHOS_CAIXA = {
      pequena: { rotulo: 'Caixas pequenas', pesoKg: 8, comprimentoCm: 40, larguraCm: 30, alturaCm: 30 },
      media: { rotulo: 'Caixas médias', pesoKg: 15, comprimentoCm: 50, larguraCm: 40, alturaCm: 40 },
      grande: { rotulo: 'Caixas grandes', pesoKg: 25, comprimentoCm: 60, larguraCm: 50, alturaCm: 50 },
    };

    function numeroDoCampo(campo) {
      if (!campo) return 0;
      var n = Number(String(campo.value).replace(',', '.'));
      return isFinite(n) ? n : 0;
    }

    function campoNumerico(nome) {
      return $('input[name="' + nome + '"]', montador);
    }

    function quantidadeDa(linha) {
      var campo = $('[data-qtd]', linha);
      var n = campo ? Math.floor(Number(String(campo.value).replace(',', '.'))) : 0;
      if (!isFinite(n) || n < 0) n = 0;
      return Math.min(20, n);
    }

    function lerCaixas() {
      var campoQtd = $('[data-caixas-qtd]', montador);
      var quantidade = Math.floor(numeroDoCampo(campoQtd));
      if (!isFinite(quantidade) || quantidade <= 0) return null;
      quantidade = Math.min(200, quantidade);

      var select = $('[data-caixas-tamanho]', montador);
      var escolha = select ? select.value : 'media';
      var medidas = TAMANHOS_CAIXA[escolha] || TAMANHOS_CAIXA.media;
      var nomeCaixas = 'Caixas ' + medidas.rotulo.toLowerCase() + 's';
      var fragil = false;
      var campoFragil = $('[data-caixas-fragil]', montador);
      if (campoFragil && campoFragil.value === '1') fragil = true;

      if (escolha === 'personalizada') {
        var comprimento = numeroDoCampo(campoNumerico('caixas_comprimento'));
        var largura = numeroDoCampo(campoNumerico('caixas_largura'));
        var altura = numeroDoCampo(campoNumerico('caixas_altura'));
        var peso = numeroDoCampo(campoNumerico('caixas_peso'));
        var valido =
          comprimento >= 10 &&
          comprimento <= 250 &&
          largura >= 10 &&
          largura <= 200 &&
          altura >= 10 &&
          altura <= 200 &&
          peso > 0 &&
          peso <= 500;
        if (valido) {
          medidas = {
            rotulo: 'Caixas (medida informada)',
            pesoKg: peso,
            comprimentoCm: comprimento,
            larguraCm: largura,
            alturaCm: altura,
          };
          nomeCaixas = medidas.rotulo;
        } else {
          nomeCaixas = 'Caixas médias';
        }
      }

      return {
        id: 'caixas',
        nome: fragil ? nomeCaixas + ' (frágil)' : nomeCaixas,
        categoria: 'Caixas e volumes',
        emoji: '📦',
        fragil: fragil,
        empilhavel: true,
        quantidade: quantidade,
        pesoKg: medidas.pesoKg,
        comprimentoCm: medidas.comprimentoCm,
        larguraCm: medidas.larguraCm,
        alturaCm: medidas.alturaCm,
      };
    }

    function coletarCarga() {
      var itens = [];
      var total = 0;

      var caixas = lerCaixas();
      if (caixas) {
        itens.push(caixas);
        total += caixas.quantidade;
      }

      $$('[data-item-carga]', montador).forEach(function (linha) {
        var quantidade = quantidadeDa(linha);
        if (quantidade <= 0) return;
        var restante = 200 - total;
        if (restante <= 0) return;
        quantidade = Math.min(quantidade, restante);
        total += quantidade;
        itens.push({
          id: linha.getAttribute('data-item-id') || 'item',
          nome: linha.getAttribute('data-nome') || 'Item',
          emoji: linha.getAttribute('data-emoji') || '📦',
          categoria: linha.getAttribute('data-categoria') || 'Outros',
          fragil: linha.getAttribute('data-fragil') === '1',
          empilhavel: linha.getAttribute('data-empilhavel') !== '0',
          quantidade: quantidade,
          pesoKg: Number(linha.getAttribute('data-peso')) || 0,
          comprimentoCm: Number(linha.getAttribute('data-comprimento')) || 0,
          larguraCm: Number(linha.getAttribute('data-largura')) || 0,
          alturaCm: Number(linha.getAttribute('data-altura')) || 0,
        });
      });

      var pesoKg = 0;
      var volumeM3 = 0;
      itens.forEach(function (item) {
        pesoKg += item.pesoKg * item.quantidade;
        volumeM3 +=
          ((item.comprimentoCm * item.larguraCm * item.alturaCm) / 1000000) * item.quantidade;
      });

      return { itens: itens, total: total, pesoKg: pesoKg, volumeM3: volumeM3 };
    }

    function analisarComVeiculo(carga, veiculo) {
      var volumeVeiculo =
        (veiculo.comprimentoCm * veiculo.larguraCm * veiculo.alturaCm) / 1000000;
      if (!(volumeVeiculo > 0) || !(veiculo.capacidadeKg > 0)) return null;

      var naoCabe = false;
      carga.itens.forEach(function (item) {
        if (item.alturaCm > veiculo.alturaCm) naoCabe = true;
        var cabeNoPiso =
          (item.comprimentoCm <= veiculo.comprimentoCm && item.larguraCm <= veiculo.larguraCm) ||
          (item.comprimentoCm <= veiculo.larguraCm && item.larguraCm <= veiculo.comprimentoCm);
        if (!cabeNoPiso) naoCabe = true;
      });

      var ocupacaoVolume = carga.volumeM3 / volumeVeiculo;
      var ocupacaoPeso = carga.pesoKg / veiculo.capacidadeKg;
      if (naoCabe || ocupacaoVolume > 1 || ocupacaoPeso > 1) return { status: 'nao_cabe' };
      if (ocupacaoVolume > 0.85 || ocupacaoPeso > 0.9) return { status: 'limite' };
      return { status: 'cabe' };
    }

    function recomendarVeiculo(carga) {
      if (!carga.total || !veiculosCarga.length) return null;

      // A conta de volume sozinha não basta: o veículo precisa acomodar o
      // plano de carga inteiro, sem deixar volumes fora por comprimento ou
      // largura. A recomendação nasce do mesmo packer que desenha o plano.
      for (var i = 0; i < veiculosCarga.length; i++) {
        var analise = analisarComVeiculo(carga, veiculosCarga[i]);
        if (!analise || analise.status === 'nao_cabe') continue;
        var plano = montarBlocos(carga, veiculosCarga[i], veiculosCarga[i].capacidadeKg);
        if (plano.fora === 0) {
          return { veiculo: veiculosCarga[i], analise: analise };
        }
      }
      return null;
    }

    // ----- plano de carregamento: o mesmo desenho do servidor, em versão resumida -----
    var PASSO_PLANO = 5;
    var ALTURA_PLANO = 180;
    var LARGURA_MAXIMA_PLANO = 900;
    var TOPO_PLANO = 74;
    var BASE_PLANO = 48;
    var CARGO_X_PLANO = 88;
    var ALTURA_PESSOA_PLANO = 170;
    var ALTURA_PESSOA_DESENHO_PLANO = 110;

    function classeCategoria(categoria) {
      if (categoria === 'Móveis') return 'moveis';
      if (categoria === 'Eletrodomésticos') return 'eletrodomesticos';
      if (categoria === 'Caixas e volumes') return 'caixas';
      return 'outros';
    }

    function escaparHtml(texto) {
      return String(texto).replace(/[&<>"']/g, function (letra) {
        if (letra === '&') return '&amp;';
        if (letra === '<') return '&lt;';
        if (letra === '>') return '&gt;';
        if (letra === '"') return '&quot;';
        return '&#39;';
      });
    }

    function cortarTexto(texto, limite) {
      if (limite < 4) return '';
      if (texto.length <= limite) return texto;
      return texto.slice(0, limite - 1).replace(/\s+$/, '') + '…';
    }

    // Nome curto para caber no desenho; o título completo fica no tooltip.
    function rotuloCurto(nome) {
      if (!/^caixa/i.test(nome)) return nome;
      var n = String(nome).toLowerCase();
      if (n.indexOf('pequen') >= 0) return 'Cx. P';
      if (n.indexOf('medida') >= 0) return 'Cx. medida';
      if (n.indexOf('médi') >= 0 || n.indexOf('medi') >= 0) return 'Cx. M';
      if (n.indexOf('grande') >= 0) return 'Cx. G';
      if (n.indexOf('livro') >= 0) return 'Cx. livros';
      if (n.indexOf('cozinha') >= 0) return 'Cx. cozinha';
      if (n.indexOf('arquivo') >= 0 || n.indexOf('escrit') >= 0) return 'Cx. arquivo';
      return 'Cx. medida';
    }

    function orientarItem(item, veiculo) {
      var opcoes = [
        { comprimento: item.comprimentoCm, profundidade: item.larguraCm },
        { comprimento: item.larguraCm, profundidade: item.comprimentoCm },
      ].filter(function (opcao) {
        return (
          opcao.comprimento > 0 &&
          opcao.profundidade > 0 &&
          opcao.comprimento <= veiculo.comprimentoCm &&
          opcao.profundidade <= veiculo.larguraCm &&
          item.alturaCm <= veiculo.alturaCm
        );
      });
      opcoes.sort(function (a, b) {
        return a.comprimento - b.comprimento;
      });
      return opcoes[0] || null;
    }

    function volumeDoItem(item) {
      return (item.comprimentoCm * item.larguraCm * item.alturaCm) / 1000000;
    }

    function resumoCategorias(carga) {
      var ordem = ['Móveis', 'Eletrodomésticos', 'Caixas e volumes', 'Outros'];
      var mapa = {};
      ordem.forEach(function (categoria) {
        mapa[categoria] = { categoria: categoria, volumes: 0, volumeM3: 0, pesoKg: 0 };
      });
      carga.itens.forEach(function (item) {
        var quantidade = Math.max(0, Math.floor(item.quantidade));
        if (quantidade === 0) return;
        var registro = mapa[item.categoria];
        if (!registro) {
          registro = { categoria: item.categoria, volumes: 0, volumeM3: 0, pesoKg: 0 };
          mapa[item.categoria] = registro;
        }
        registro.volumes += quantidade;
        registro.volumeM3 += volumeDoItem(item) * quantidade;
        registro.pesoKg += item.pesoKg * quantidade;
      });
      return Object.keys(mapa)
        .map(function (chave) {
          return mapa[chave];
        })
        .filter(function (registro) {
          return registro.volumes > 0;
        })
        .map(function (registro) {
          return {
            categoria: registro.categoria,
            volumes: registro.volumes,
            volumeM3: Math.round(registro.volumeM3 * 1000) / 1000,
            pesoKg: Math.round(registro.pesoKg * 100) / 100,
          };
        });
    }

    function montarBlocos(carga, veiculo, pesoMaximoKg) {
      var unidades = [];
      var fora = 0;
      var foraPorPeso = 0;
      var pesoColocado = 0;
      var pesoMaximo =
        pesoMaximoKg == null ? Number(veiculo.capacidadeKg) || 0 : Number(pesoMaximoKg) || 0;

      carga.itens.forEach(function (item) {
        var quantidade = Math.max(0, Math.floor(item.quantidade));
        var orientacao = orientarItem(item, veiculo);
        if (!orientacao) {
          fora += quantidade;
          return;
        }
        for (var i = 0; i < quantidade; i++) {
          unidades.push({
            item: item,
            comprimento: orientacao.comprimento,
            profundidade: orientacao.profundidade,
            altura: item.alturaCm,
            empilhavel: item.empilhavel !== false && !item.fragil,
          });
        }
      });

      unidades.sort(function (a, b) {
        var pisoA = a.empilhavel ? 1 : 0;
        var pisoB = b.empilhavel ? 1 : 0;
        if (pisoA !== pisoB) return pisoA - pisoB;
        if (pisoA === 0) {
          if (b.altura !== a.altura) return b.altura - a.altura;
          return b.item.pesoKg - a.item.pesoKg;
        }
        var volumeA = a.item.comprimentoCm * a.item.larguraCm * a.item.alturaCm;
        var volumeB = b.item.comprimentoCm * b.item.larguraCm * b.item.alturaCm;
        if (volumeB !== volumeA) return volumeB - volumeA;
        return a.item.pesoKg - b.item.pesoKg;
      });

      var celulas = Math.max(1, Math.ceil(veiculo.comprimentoCm / PASSO_PLANO));
      var faixas = [];
      var blocos = [];
      var larguraOcupada = 0;

      function criarFaixa(profundidade) {
        var alturas = [];
        var suporta = [];
        for (var c = 0; c < celulas; c++) {
          alturas.push(0);
          suporta.push(0);
        }
        return {
          indice: faixas.length,
          profundidade: profundidade,
          alturas: alturas,
          suporta: suporta,
          volumes: 0,
        };
      }

      function cabeProfundidade(faixa, unidade) {
        var novaLargura = Math.max(faixa.profundidade, unidade.profundidade);
        return larguraOcupada - faixa.profundidade + novaLargura <= veiculo.larguraCm + 0.5;
      }

      function confirmarProfundidade(faixa, unidade) {
        var novaLargura = Math.max(faixa.profundidade, unidade.profundidade);
        if (novaLargura <= faixa.profundidade) return;
        larguraOcupada += novaLargura - faixa.profundidade;
        faixa.profundidade = novaLargura;
      }

      function buscarPosicao(faixa, unidade, modo) {
        var larguraCelulas = Math.max(1, Math.ceil(unidade.comprimento / PASSO_PLANO));
        var melhorInicio = -1;
        var melhorBase = Infinity;

        for (var inicio = 0; inicio + larguraCelulas <= celulas; inicio++) {
          var base = faixa.alturas[inicio];
          if (modo === 'piso' ? base > 0.5 : base <= 0.5) continue;

          var plana = true;
          var i;
          for (i = inicio + 1; i < inicio + larguraCelulas; i++) {
            if (Math.abs(faixa.alturas[i] - base) > 0.5) {
              plana = false;
              break;
            }
          }
          if (!plana) continue;

          if (modo === 'empilhar') {
            var apoio = true;
            for (i = inicio; i < inicio + larguraCelulas; i++) {
              if (faixa.suporta[i] !== 1) {
                apoio = false;
                break;
              }
            }
            if (!apoio) continue;
          }

          if (base + unidade.altura > veiculo.alturaCm + 0.5) continue;

          if (base < melhorBase - 0.01) {
            melhorBase = base;
            melhorInicio = inicio;
          }
        }

        if (melhorInicio < 0) return null;
        return { inicio: melhorInicio, base: melhorBase };
      }

      function registrarBloco(faixa, unidade, posicao) {
        var larguraCelulas = Math.max(1, Math.ceil(unidade.comprimento / PASSO_PLANO));
        var topo = posicao.base + unidade.altura;
        for (var c = posicao.inicio; c < posicao.inicio + larguraCelulas; c++) {
          faixa.alturas[c] = topo;
          faixa.suporta[c] = unidade.empilhavel ? 1 : 0;
        }

        faixa.volumes += 1;
        pesoColocado += unidade.item.pesoKg;
        blocos.push({
          item: unidade.item,
          faixa: faixa.indice,
          x: posicao.inicio * PASSO_PLANO,
          base: posicao.base,
          comprimento: Math.min(
            unidade.comprimento,
            veiculo.comprimentoCm - posicao.inicio * PASSO_PLANO,
          ),
          altura: unidade.altura,
          profundidade: unidade.profundidade,
          volumeM3: volumeDoItem(unidade.item),
          pesoKg: unidade.item.pesoKg,
          empilhado: posicao.base > 0.5,
        });
      }

      unidades.forEach(function (unidade) {
        // O peso é conferido antes do encaixe, como no servidor.
        if (pesoMaximo > 0 && pesoColocado + unidade.item.pesoKg > pesoMaximo + 0.001) {
          foraPorPeso += 1;
          return;
        }

        var colocado = false;

        // 1. Piso das faixas que já existem.
        for (var f = 0; f < faixas.length; f++) {
          var faixa = faixas[f];
          if (!cabeProfundidade(faixa, unidade)) continue;
          var posicaoPiso = buscarPosicao(faixa, unidade, 'piso');
          if (!posicaoPiso) continue;
          registrarBloco(faixa, unidade, posicaoPiso);
          confirmarProfundidade(faixa, unidade);
          colocado = true;
          break;
        }

        // 2. Faixa nova no piso, usando a largura que ainda sobra.
        if (!colocado && larguraOcupada + unidade.profundidade <= veiculo.larguraCm + 0.5) {
          var novaFaixa = criarFaixa(unidade.profundidade);
          var posicaoNova = buscarPosicao(novaFaixa, unidade, 'piso');
          if (posicaoNova) {
            registrarBloco(novaFaixa, unidade, posicaoNova);
            faixas.push(novaFaixa);
            larguraOcupada += novaFaixa.profundidade;
            colocado = true;
          }
        }

        // 3. Só empilha quando o piso de todas as faixas já está ocupado.
        // Escolhe a faixa com a base mais baixa para distribuir a altura.
        if (!colocado && unidade.empilhavel) {
          var escolhida = null;
          for (var g = 0; g < faixas.length; g++) {
            var faixaEmpilhar = faixas[g];
            if (!cabeProfundidade(faixaEmpilhar, unidade)) continue;
            var posicaoEmpilhar = buscarPosicao(faixaEmpilhar, unidade, 'empilhar');
            if (!posicaoEmpilhar) continue;
            if (!escolhida || posicaoEmpilhar.base < escolhida.posicao.base - 0.01) {
              escolhida = { faixa: faixaEmpilhar, posicao: posicaoEmpilhar };
            }
          }
          if (escolhida) {
            registrarBloco(escolhida.faixa, unidade, escolhida.posicao);
            confirmarProfundidade(escolhida.faixa, unidade);
            colocado = true;
          }
        }

        if (!colocado) fora += 1;
      });

      var alturaUsada = 0;
      var comprimentoUsado = 0;
      faixas.forEach(function (faixa) {
        for (var a = 0; a < celulas; a++) {
          if (faixa.alturas[a] > alturaUsada) alturaUsada = faixa.alturas[a];
          if (faixa.alturas[a] > 0) {
            comprimentoUsado = Math.max(
              comprimentoUsado,
              Math.min(veiculo.comprimentoCm, (a + 1) * PASSO_PLANO),
            );
          }
        }
      });

      var resumoFaixas = faixas.map(function (faixa) {
        return {
          indice: faixa.indice,
          profundidadeCm: Math.round(faixa.profundidade),
          volumes: faixa.volumes,
        };
      });
      var volumesAtras = 0;
      resumoFaixas.forEach(function (faixa) {
        if (faixa.indice > 0) volumesAtras += faixa.volumes;
      });

      // Conferência item a item: todos os volumes entram na lista, inclusive
      // os que não couberam no veículo.
      var mapaItens = {};
      carga.itens.forEach(function (item) {
        var chave = String(item.id) + '|' + String(item.nome);
        var registro = mapaItens[chave];
        if (registro) {
          registro.total += item.quantidade;
          return;
        }
        mapaItens[chave] = {
          chave: chave,
          nome: item.nome,
          emoji: item.emoji,
          categoria: item.categoria,
          fragil: Boolean(item.fragil),
          total: item.quantidade,
          colocados: 0,
          fora: 0,
          volumeM3: volumeDoItem(item) * item.quantidade,
          pesoKg: item.pesoKg * item.quantidade,
          volumeColocadoM3: 0,
          pesoColocadoKg: 0,
        };
      });
      blocos.forEach(function (bloco) {
        var chave = String(bloco.item.id) + '|' + String(bloco.item.nome);
        if (mapaItens[chave]) {
          mapaItens[chave].colocados += 1;
          mapaItens[chave].volumeColocadoM3 += bloco.volumeM3;
          mapaItens[chave].pesoColocadoKg += bloco.pesoKg;
        }
      });
      var itens = [];
      var foraTotal = 0;
      Object.keys(mapaItens).forEach(function (chave) {
        var registro = mapaItens[chave];
        registro.fora = Math.max(0, registro.total - registro.colocados);
        foraTotal += registro.fora;
        itens.push(registro);
      });

      return {
        blocos: blocos,
        faixas: resumoFaixas,
        itens: itens,
        totalUnidades: carga.total,
        colocados: blocos.length,
        fora: foraTotal,
        foraPorPeso: foraPorPeso,
        pesoColocadoKg: Math.round(pesoColocado * 100) / 100,
        volumeColocadoM3:
          Math.round(
            blocos.reduce(function (total, bloco) {
              return total + bloco.volumeM3;
            }, 0) * 1000,
          ) / 1000,
        categorias: resumoCategorias(carga),
        volumesAtras: volumesAtras,
        alturaUsada: alturaUsada,
        comprimentoUsado: comprimentoUsado,
        larguraUsada: larguraOcupada,
      };
    }

    // Mesma divisão de viagens do servidor: usa o packer de verdade em cada
    // leva, em vez de estimar por volume.
    var LIMITE_VIAGENS = 12;

    function resumoDaCarga(itens) {
      var total = 0;
      var pesoKg = 0;
      var volumeM3 = 0;
      itens.forEach(function (item) {
        total += item.quantidade;
        pesoKg += item.pesoKg * item.quantidade;
        volumeM3 += volumeDoItem(item) * item.quantidade;
      });
      return { itens: itens, total: total, pesoKg: pesoKg, volumeM3: volumeM3 };
    }

    function planejarViagens(carga, veiculo, limite) {
      var limiteViagens = Math.max(1, Math.floor(limite || LIMITE_VIAGENS));
      var base = {
        veiculo: veiculo,
        viagens: [],
        totalViagens: 0,
        cabeEmUma: carga.total === 0,
        viavel: carga.total === 0,
        impossiveis: [],
        restantes: 0,
        volumes: carga.total,
        pesoKg: carga.pesoKg,
        volumeM3: carga.volumeM3,
        limiteViagens: limiteViagens,
        motivo: carga.total === 0 ? 'Sem volumes para planejar.' : '',
      };
      if (carga.total === 0) return base;

      var restantesItens = carga.itens
        .map(function (item) {
          return Object.assign({}, item, {
            quantidade: Math.max(0, Math.floor(item.quantidade)),
          });
        })
        .filter(function (item) {
          return item.quantidade > 0;
        });
      var impossiveis = [];

      restantesItens = restantesItens.filter(function (item) {
        if (orientarItem(item, veiculo)) return true;
        impossiveis.push({
          nome: item.nome,
          emoji: item.emoji,
          quantidade: item.quantidade,
          motivo: 'não passa nas medidas do veículo',
        });
        return false;
      });

      var viagens = [];
      while (restantesItens.length > 0 && viagens.length < limiteViagens) {
        var resumo = resumoDaCarga(restantesItens);
        var montagem = montarBlocos(resumo, veiculo, veiculo.capacidadeKg);
        if (montagem.colocados === 0) break;

        var colocadosPorChave = {};
        montagem.itens.forEach(function (item) {
          colocadosPorChave[item.chave] = item.colocados;
        });
        var proximos = [];
        var itensViagem = [];

        restantesItens.forEach(function (item) {
          var chave = String(item.id) + '|' + String(item.nome);
          var colocados = colocadosPorChave[chave] || 0;
          if (colocados > 0) {
            itensViagem.push({
              nome: item.nome,
              emoji: item.emoji,
              quantidade: colocados,
            });
          }
          var sobra = item.quantidade - colocados;
          if (sobra > 0) proximos.push(Object.assign({}, item, { quantidade: sobra }));
        });

        if (itensViagem.length === 0) break;

        viagens.push({
          numero: viagens.length + 1,
          montagem: montagem,
          itens: itensViagem,
          pesoKg: montagem.pesoColocadoKg,
          volumeM3: montagem.volumeColocadoM3,
        });
        restantesItens = proximos;
      }

      var restantes = restantesItens.reduce(function (total, item) {
        return total + item.quantidade;
      }, 0);

      if (restantes > 0) {
        var porItem = {};
        restantesItens.forEach(function (item) {
          var chave = String(item.id) + '|' + String(item.nome);
          if (porItem[chave]) {
            porItem[chave].quantidade += item.quantidade;
          } else {
            porItem[chave] = {
              nome: item.nome,
              emoji: item.emoji,
              quantidade: item.quantidade,
              motivo:
                viagens.length >= limiteViagens
                  ? 'passa do limite de ' + limiteViagens + ' viagens'
                  : 'não coube no desenho do veículo',
            };
          }
        });
        Object.keys(porItem).forEach(function (chave) {
          impossiveis.push(porItem[chave]);
        });
      }

      var totalViagens = viagens.length;
      var totalImpossiveis = impossiveis.reduce(function (total, item) {
        return total + item.quantidade;
      }, 0);
      var cabeEmUma = totalViagens === 1 && restantes === 0 && impossiveis.length === 0;
      var viavel = restantes === 0 && impossiveis.length === 0;
      var motivo = '';
      if (cabeEmUma) {
        motivo = 'Tudo cabe em 1 viagem.';
      } else if (viavel) {
        motivo =
          'A carga não cabe de uma vez. São necessárias ' +
          totalViagens +
          ' viagens com este veículo.';
      } else if (restantes > 0 && totalViagens >= limiteViagens) {
        motivo =
          'Com ' +
          totalViagens +
          ' viagens ainda sobram ' +
          restantes +
          (restantes === 1 ? ' volume' : ' volumes') +
          '. Confirme o veículo ou divida a mudança.';
      } else if (totalViagens === 0) {
        motivo =
          'Nenhum volume deste veículo entra no desenho. Confira as medidas ou escolha um veículo maior.';
      } else {
        motivo =
          'São necessárias ' +
          totalViagens +
          ' viagens, mas ' +
          totalImpossiveis +
          (totalImpossiveis === 1 ? ' volume não entra' : ' volumes não entram') +
          ' neste veículo.';
      }

      return {
        veiculo: veiculo,
        viagens: viagens,
        totalViagens: totalViagens,
        cabeEmUma: cabeEmUma,
        viavel: viavel,
        impossiveis: impossiveis,
        restantes: restantes,
        volumes: carga.total,
        pesoKg: carga.pesoKg,
        volumeM3: carga.volumeM3,
        limiteViagens: limiteViagens,
        motivo: motivo,
      };
    }

    function temBlocoAbaixo(bloco, blocos) {
      for (var i = 0; i < blocos.length; i++) {
        var outro = blocos[i];
        if (
          outro !== bloco &&
          outro.faixa === bloco.faixa &&
          outro.x === bloco.x &&
          Math.abs(outro.base + outro.altura - bloco.base) < 0.6 &&
          outro.item.id === bloco.item.id
        ) {
          return true;
        }
      }
      return false;
    }

    function pessoaSvg(papel, x, pisoY, escala) {
      var camisa = papel === 'cliente' ? '#0f766e' : papel === 'motorista' ? '#1d4ed8' : '#f59e0b';
      var detalhe = papel === 'cliente' ? '#134e4a' : papel === 'motorista' ? '#172554' : '#b45309';
      var rotulo =
        papel === 'cliente' ? 'dono da mudança' : papel === 'motorista' ? 'motorista' : 'ajudante';
      var s = '';
      s +=
        '<g class="plano-pessoa plano-pessoa--' +
        papel +
        '" transform="translate(' +
        x.toFixed(1) +
        ' ' +
        pisoY.toFixed(1) +
        ')">';
      s += '<g transform="scale(' + escala.toFixed(3) + ')">';
      s += '<ellipse cx="0" cy="2" rx="24" ry="5" class="plano-pessoa__sombra"/>';
      s += '<path d="M-11 -68l-9 16M11 -68l9 16" class="plano-pessoa__braco"/>';
      s += '<rect x="-11" y="-78" width="22" height="48" rx="8" fill="' + camisa + '"/>';
      if (papel === 'ajudante') {
        s += '<rect x="-8" y="-73" width="16" height="21" rx="4" fill="#fef3c7" opacity=".9"/>';
        s += '<path d="M-11 -64h22" stroke="#fff" stroke-width="2.2" opacity=".85"/>';
      }
      s +=
        '<rect x="-19" y="-64" width="38" height="34" rx="3" fill="#fbbf24" stroke="#d97706" stroke-width="1.4" class="plano-pessoa__caixa"/>';
      s += '<path d="M-19 -52h38M0 -64v34" class="plano-pessoa__fita"/>';
      s += '<circle cx="-21" cy="-50" r="4.5" fill="#fcd9b6"/>';
      s += '<circle cx="21" cy="-50" r="4.5" fill="#fcd9b6"/>';
      s += '<circle cx="0" cy="-94" r="13" fill="#fcd9b6"/>';
      if (papel === 'cliente') {
        s +=
          '<path d="M-12 -99c2-7 9-11 16-9 4 1 7 4 8 9-4-3-9-4-14-3-4 0-7 2-10 3Z" fill="#3f2a1d"/>';
      } else {
        s += '<path d="M-13 -99a13 13 0 0 1 26 0Z" fill="' + detalhe + '"/>';
        s += '<path d="M-15 -99h30" stroke="' + detalhe + '" stroke-width="3" stroke-linecap="round"/>';
      }
      s += '<rect x="-9" y="-32" width="7" height="32" rx="3.5" fill="#1e293b"/>';
      s += '<rect x="2" y="-32" width="7" height="32" rx="3.5" fill="#1e293b"/>';
      s += '</g>';
      s += '<text x="0" y="24" text-anchor="middle" class="plano-pessoa__legenda">' + rotulo + '</text>';
      s += '</g>';
      return s;
    }

    function listaHtml(dados, viagens) {
      var itens = dados.itens || [];
      if (!itens.length) return '';
      var fora = itens.filter(function (item) {
        return item.fora > 0;
      });
      var proximas = viagens && !viagens.cabeEmUma && viagens.viavel && viagens.totalViagens > 1;
      var html = '<div class="plano-conferencia" data-plano-conferencia>';

      if (fora.length) {
        html +=
          '<div class="plano-fora" data-plano-fora><strong>' +
          (proximas ? 'Vão nas próximas viagens' : 'Ficaram fora do plano') +
          '</strong><ul>';
        fora.forEach(function (item) {
          html +=
            '<li><span aria-hidden="true">' +
            escaparHtml(item.emoji || '📦') +
            '</span><span><b>' +
            escaparHtml(item.nome) +
            '</b> — ' +
            item.fora +
            ' de ' +
            item.total +
            (item.total === 1 ? ' volume' : ' volumes') +
            '</span></li>';
        });
        html +=
          '</ul><p>' +
          (proximas
            ? 'Estes volumes não couberam na primeira viagem e entram nas próximas. A lista de viagens mostra a divisão.'
            : viagens && viagens.viavel === false
              ? 'Estes volumes não entram neste veículo. Confira as medidas ou escolha um veículo maior.'
              : 'O veículo escolhido não acomoda tudo. Confira as medidas ou escolha um veículo maior.') +
          '</p></div>';
      }

      html +=
        '<div class="plano-lista" data-plano-itens><div class="plano-lista__topo"><strong>Conferência dos volumes</strong><span>Todos os itens da lista, inclusive os que ficaram fora</span></div><ul>';
      itens.forEach(function (item) {
        var completo = item.fora === 0;
        html +=
          '<li class="plano-lista__item' +
          (completo ? '' : ' plano-lista__item--fora') +
          '"><span class="plano-lista__emoji" aria-hidden="true">' +
          escaparHtml(item.emoji || '📦') +
          '</span><span class="plano-lista__nome">' +
          escaparHtml(item.nome) +
          '<small>' +
          item.total +
          (item.total === 1 ? ' volume' : ' volumes') +
          ' · ' +
          fmtNumero.format(item.volumeM3 || 0) +
          ' m³</small></span><span class="plano-lista__status">' +
          (completo ? 'No plano' : proximas ? item.fora + ' na próxima' : item.fora + ' fora') +
          '</span></li>';
      });
      html += '</ul></div></div>';
      return html;
    }

    function categoriasHtml(dados, carga) {
      var categorias = dados.categorias || [];
      if (!categorias.length) return '';
      var html =
        '<div class="plano-categorias" data-plano-categorias><div class="plano-categorias__topo"><strong>Quanto espaço cada grupo ocupa</strong><span>Volume total: ' +
        fmtNumero.format(carga.volumeM3) +
        ' m³</span></div><ul class="plano-categorias__lista">';
      categorias.forEach(function (categoria) {
        html +=
          '<li class="plano-categoria plano-categoria--' +
          classeCategoria(categoria.categoria) +
          '"><span class="plano-categoria__nome">' +
          escaparHtml(categoria.categoria) +
          '</span><strong>' +
          fmtNumero.format(categoria.volumeM3) +
          ' m³</strong><small>' +
          categoria.volumes +
          (categoria.volumes === 1 ? ' volume' : ' volumes') +
          ' · ' +
          fmtPesoCarga.format(Math.round(categoria.pesoKg)) +
          ' kg</small></li>';
      });
      html += '</ul></div>';
      return html;
    }

    function viagensHtml(viagens) {
      if (!viagens || viagens.volumes === 0) return '';
      var icone = viagens.cabeEmUma ? '✅' : viagens.viavel ? '🔁' : '⚠️';
      var titulo = viagens.cabeEmUma
        ? 'Tudo cabe em 1 viagem'
        : viagens.viavel
          ? 'São necessárias ' + viagens.totalViagens + ' viagens'
          : 'Este veículo não leva tudo';
      var html =
        '<div class="plano-viagens' +
        (viagens.cabeEmUma ? ' plano-viagens--ok' : '') +
        '" data-plano-viagens><div class="plano-viagens__topo"><span class="plano-viagens__icone" aria-hidden="true">' +
        icone +
        '</span><div><strong>' +
        titulo +
        '</strong><p>' +
        escaparHtml(viagens.motivo) +
        '</p></div></div>';

      if (viagens.totalViagens > 1) {
        html += '<ol class="plano-viagens__lista">';
        viagens.viagens.forEach(function (viagem) {
          var itens = viagem.itens
            .map(function (item) {
              return item.quantidade + '× ' + (item.emoji || '📦') + ' ' + item.nome;
            })
            .join(' · ');
          html +=
            '<li class="plano-viagem"><div class="plano-viagem__titulo"><strong>Viagem ' +
            viagem.numero +
            '</strong><span>' +
            fmtNumero.format(viagem.volumeM3) +
            ' m³ · ' +
            fmtPesoCarga.format(Math.round(viagem.pesoKg)) +
            ' kg</span></div><p>' +
            escaparHtml(itens) +
            '</p></li>';
        });
        html += '</ol>';
      }

      if (viagens.impossiveis.length) {
        html +=
          '<div class="plano-viagens__impossiveis"><strong>O que não entra neste veículo</strong><ul>';
        viagens.impossiveis.forEach(function (item) {
          html +=
            '<li><span aria-hidden="true">' +
            escaparHtml(item.emoji || '📦') +
            '</span> ' +
            item.quantidade +
            '× ' +
            escaparHtml(item.nome) +
            ' — ' +
            escaparHtml(item.motivo) +
            '</li>';
        });
        html += '</ul></div>';
      }

      html += '</div>';
      return html;
    }

    function coresCategoria(categoria) {
      if (categoria === 'Móveis') {
        return { topo: '#dbeafe', frente: '#93c5fd', lado: '#60a5fa', traco: '#2563eb' };
      }
      if (categoria === 'Eletrodomésticos') {
        return { topo: '#fef3c7', frente: '#fcd34d', lado: '#f59e0b', traco: '#b45309' };
      }
      if (categoria === 'Caixas e volumes') {
        return { topo: '#dcfce7', frente: '#86efac', lado: '#4ade80', traco: '#047857' };
      }
      return { topo: '#f1f5f9', frente: '#cbd5e1', lado: '#94a3b8', traco: '#64748b' };
    }

    var ISO_COSSENO = Math.cos(Math.PI / 6);
    var ISO_SENO = 0.5;

    function pontoIso(x, y, z, origem, escala) {
      return {
        x: origem.x + (x - z) * ISO_COSSENO * escala,
        y: origem.y + (x + z) * ISO_SENO * escala - y * escala,
      };
    }

    function pontosPoly(pontos) {
      return pontos
        .map(function (ponto) {
          return ponto.x.toFixed(1) + ',' + ponto.y.toFixed(1);
        })
        .join(' ');
    }

    function vistaIsoHtml(dados, veiculo, carga) {
      var comprimento = Math.max(1, veiculo.comprimentoCm);
      var largura = Math.max(1, veiculo.larguraCm);
      var altura = Math.max(1, veiculo.alturaCm);
      var larguraView = 880;
      var margem = 46;
      var escala = Math.max(
        0.05,
        Math.min(
          (larguraView - margem * 2) / ((comprimento + largura) * ISO_COSSENO),
          430 / ((comprimento + largura) * ISO_SENO + altura),
        ),
      );
      var larguraDesenho = (comprimento + largura) * ISO_COSSENO * escala;
      var alturaDesenho = (comprimento + largura) * ISO_SENO * escala + altura * escala;
      var alturaView = margem * 2 + alturaDesenho;
      var origem = {
        x: (larguraView - larguraDesenho) / 2 + largura * ISO_COSSENO * escala,
        y: (alturaView - alturaDesenho) / 2 + altura * escala,
      };

      function p(x, y, z) {
        return pontoIso(x, y, z, origem, escala);
      }

      var faixaOffset = {};
      var acumulado = 0;
      dados.faixas.forEach(function (faixa) {
        faixaOffset[faixa.indice] = acumulado;
        acumulado += faixa.profundidadeCm;
      });

      var blocosOrdenados = dados.blocos.slice().sort(function (a, b) {
        var za = faixaOffset[a.faixa] || 0;
        var zb = faixaOffset[b.faixa] || 0;
        return a.x + za - (b.x + zb);
      });

      var html =
        '<svg class="plano-iso" viewBox="0 0 ' +
        larguraView.toFixed(0) +
        ' ' +
        alturaView.toFixed(0) +
        '" role="img" aria-label="' +
        (carga.total === 0
          ? 'Vista 3D de um veículo vazio, pronto para receber a mudança'
          : 'Vista 3D da carga dentro de ' +
            escaparHtml(veiculo.nome || 'um veículo') +
            ', com ' +
            dados.colocados +
            ' volumes') +
        '"><title>Vista 3D do carregamento</title><desc>Cada caixa representa um volume. A frente fica na parte de cima do desenho e a porta na parte de baixo. As faces mais claras são o topo dos volumes.</desc>';

      html +=
        '<polygon points="' +
        pontosPoly([
          p(comprimento, 0, 0),
          p(comprimento, altura, 0),
          p(comprimento, altura, largura),
          p(comprimento, 0, largura),
        ]) +
        '" class="plano-iso__parede"/>';
      html +=
        '<polygon points="' +
        pontosPoly([p(0, 0, 0), p(comprimento, 0, 0), p(comprimento, altura, 0), p(0, altura, 0)]) +
        '" class="plano-iso__parede plano-iso__parede--lado"/>';
      html +=
        '<polygon points="' +
        pontosPoly([
          p(0, 0, 0),
          p(comprimento, 0, 0),
          p(comprimento, 0, largura),
          p(0, 0, largura),
        ]) +
        '" class="plano-iso__piso"/>';

      dados.faixas.forEach(function (faixa) {
        var z = faixaOffset[faixa.indice] || 0;
        if (z <= 0) return;
        var inicio = p(0, 0, z);
        var fim = p(comprimento, 0, z);
        html +=
          '<path d="M' +
          inicio.x.toFixed(1) +
          ' ' +
          inicio.y.toFixed(1) +
          'L' +
          fim.x.toFixed(1) +
          ' ' +
          fim.y.toFixed(1) +
          '" class="plano-iso__faixa"/>';
      });

      blocosOrdenados.forEach(function (bloco) {
        var z0 = faixaOffset[bloco.faixa] || 0;
        var z1 = z0 + bloco.profundidade;
        var x0 = bloco.x;
        var x1 = bloco.x + bloco.comprimento;
        var y0 = bloco.base;
        var y1 = bloco.base + bloco.altura;
        var cores = coresCategoria(bloco.item.categoria);
        var topo = [p(x0, y1, z0), p(x1, y1, z0), p(x1, y1, z1), p(x0, y1, z1)];
        var faceFrente = [p(x1, y0, z0), p(x1, y0, z1), p(x1, y1, z1), p(x1, y1, z0)];
        var faceLado = [p(x0, y0, z1), p(x1, y0, z1), p(x1, y1, z1), p(x0, y1, z1)];

        html +=
          '<g class="plano-iso__bloco plano-bloco--' +
          classeCategoria(bloco.item.categoria) +
          (bloco.item.fragil ? ' plano-bloco--fragil' : '') +
          '"><title>' +
          escaparHtml(bloco.item.nome) +
          (bloco.item.fragil ? ' (frágil)' : '') +
          ' · ' +
          Math.round(bloco.comprimento) +
          ' × ' +
          Math.round(bloco.profundidade) +
          ' × ' +
          Math.round(bloco.altura) +
          ' cm · base a ' +
          Math.round(bloco.base) +
          ' cm</title>';
        html +=
          '<polygon points="' +
          pontosPoly(faceLado) +
          '" fill="' +
          cores.lado +
          '" stroke="' +
          cores.traco +
          '" class="plano-iso__face plano-iso__face--lado"/>';
        html +=
          '<polygon points="' +
          pontosPoly(faceFrente) +
          '" fill="' +
          cores.frente +
          '" stroke="' +
          cores.traco +
          '" class="plano-iso__face plano-iso__face--frente"/>';
        html +=
          '<polygon points="' +
          pontosPoly(topo) +
          '" fill="' +
          cores.topo +
          '" stroke="' +
          cores.traco +
          '" class="plano-iso__face plano-iso__face--topo"/></g>';
      });

      var contorno = [p(0, altura, 0), p(comprimento, altura, 0), p(comprimento, altura, largura)];
      html +=
        '<path d="M' +
        contorno[0].x.toFixed(1) +
        ' ' +
        contorno[0].y.toFixed(1) +
        'L' +
        contorno[1].x.toFixed(1) +
        ' ' +
        contorno[1].y.toFixed(1) +
        'L' +
        contorno[2].x.toFixed(1) +
        ' ' +
        contorno[2].y.toFixed(1) +
        '" class="plano-iso__contorno"/>';

      var frente = p(0, 0, 0);
      var porta = p(comprimento, 0, largura);
      html +=
        '<text x="' +
        frente.x.toFixed(1) +
        '" y="' +
        (frente.y - 12).toFixed(1) +
        '" text-anchor="middle" class="plano-iso__rotulo">frente (cabine)</text>';
      html +=
        '<text x="' +
        porta.x.toFixed(1) +
        '" y="' +
        (porta.y + 20).toFixed(1) +
        '" text-anchor="middle" class="plano-iso__rotulo">porta</text>';

      if (carga.total === 0) {
        html +=
          '<g class="plano-iso__vazio"><rect x="' +
          (origem.x - 95).toFixed(1) +
          '" y="' +
          (origem.y - 28).toFixed(1) +
          '" width="190" height="52" rx="8" class="plano-iso__vazio-caixa"/><text x="' +
          origem.x.toFixed(1) +
          '" y="' +
          (origem.y + 4).toFixed(1) +
          '" text-anchor="middle" class="plano-iso__vazio-texto">escolha os itens para montar a carga em 3D</text></g>';
      }

      html += '</svg>';
      return html;
    }

    function vistaHtml(dados, veiculo) {
      if (!dados.faixas.length) return '';

      var comprimento = Math.max(1, veiculo.comprimentoCm);
      var largura = Math.max(1, veiculo.larguraCm);
      var escala = Math.min(620 / comprimento, 190 / largura);
      var vistaLargura = comprimento * escala;
      var vistaAltura = largura * escala;
      var padX = 26;
      var padY = 26;
      var viewLargura = padX + vistaLargura + 40;
      var viewAltura = padY + vistaAltura + 34;
      var offsets = {};
      var acumulado = 0;
      dados.faixas.forEach(function (faixa) {
        offsets[faixa.indice] = acumulado;
        acumulado += faixa.profundidadeCm;
      });
      var sobra = Math.max(0, largura - dados.larguraUsada);

      var html = '<div class="plano-vista" data-plano-vista>';
      html +=
        '<div class="plano-vista__topo"><strong>Vista de cima real</strong><span>' +
        Math.round(comprimento) +
        ' × ' +
        Math.round(largura) +
        ' cm · ' +
        dados.faixas.length +
        (dados.faixas.length === 1 ? ' faixa de largura' : ' faixas de largura') +
        '</span></div>';
      html +=
        '<svg viewBox="0 0 ' +
        viewLargura.toFixed(1) +
        ' ' +
        viewAltura.toFixed(1) +
        '" class="plano-vista__svg" role="img" aria-label="Vista de cima proporcional da carroceria, com ' +
        dados.colocados +
        ' volumes posicionados e ' +
        dados.fora +
        ' fora">';
      html += '<title>Vista de cima da carroceria</title>';
      html +=
        '<rect x="' +
        padX +
        '" y="' +
        padY +
        '" width="' +
        vistaLargura.toFixed(1) +
        '" height="' +
        vistaAltura.toFixed(1) +
        '" rx="6" class="plano-vista__piso"/>';
      html +=
        '<text x="' +
        padX +
        '" y="' +
        (padY - 8) +
        '" class="plano-vista__rotulo">frente (cabine)</text>';
      html +=
        '<text x="' +
        (padX + vistaLargura).toFixed(1) +
        '" y="' +
        (padY - 8) +
        '" text-anchor="end" class="plano-vista__rotulo">porta</text>';
      if (sobra > 0.5) {
        html +=
          '<rect x="' +
          padX +
          '" y="' +
          (padY + dados.larguraUsada * escala).toFixed(1) +
          '" width="' +
          vistaLargura.toFixed(1) +
          '" height="' +
          Math.max(1, sobra * escala).toFixed(1) +
          '" rx="3" class="plano-vista__sobra"/>';
      }
      dados.blocos.forEach(function (bloco) {
        var offset = offsets[bloco.faixa] || 0;
        var x = padX + bloco.x * escala;
        var y = padY + offset * escala;
        var larguraBloco = Math.max(1.4, bloco.comprimento * escala - 0.8);
        var alturaBloco = Math.max(1.4, bloco.profundidade * escala - 0.8);
        var fragil = bloco.item.fragil;
        html +=
          '<g class="plano-vista__bloco plano-bloco--' +
          classeCategoria(bloco.item.categoria) +
          (fragil ? ' plano-bloco--fragil' : '') +
          '">';
        html +=
          '<title>' +
          escaparHtml(bloco.item.nome) +
          (fragil ? ' (frágil)' : '') +
          ' · faixa ' +
          (bloco.faixa + 1) +
          ' · base ' +
          Math.round(bloco.comprimento) +
          ' × ' +
          Math.round(bloco.profundidade) +
          ' cm</title>';
        html +=
          '<rect x="' +
          x.toFixed(1) +
          '" y="' +
          y.toFixed(1) +
          '" width="' +
          larguraBloco.toFixed(1) +
          '" height="' +
          alturaBloco.toFixed(1) +
          '" rx="2" class="plano-vista__caixa"/>';
        html += '</g>';
      });
      html += '</svg>';
      html +=
        '<p class="plano-vista__nota">Cada retângulo é a base de um volume. Volumes empilhados aparecem na mesma posição; a altura usada está no corte lateral.</p>';
      html += '</div>';
      return html;
    }

    function svgDoPlano(carga, veiculo, ajudantes, viagens) {
      var alturaCm = Math.max(1, veiculo.alturaCm);
      var comprimentoCm = Math.max(1, veiculo.comprimentoCm);
      var escala = Math.min(ALTURA_PLANO / alturaCm, LARGURA_MAXIMA_PLANO / comprimentoCm);
      var internaL = Math.max(40, comprimentoCm * escala);
      var internaA = Math.max(24, alturaCm * escala);
      var escalaPessoa = (ALTURA_PESSOA_PLANO * escala) / ALTURA_PESSOA_DESENHO_PLANO;
      var meiaPessoa = 24 * escalaPessoa;
      var espacamentoPessoa = 48 * escalaPessoa + 12;
      var pisoY = TOPO_PLANO + internaA;
      var totalAjudantes = Math.max(0, Math.floor(ajudantes || 0));
      var papeis = totalAjudantes === 0 ? ['cliente'] : ['motorista'];
      var nAjudantes = Math.min(2, totalAjudantes);
      for (var a = 0; a < nAjudantes; a++) papeis.push('ajudante');
      var pessoaBaseX = CARGO_X_PLANO + internaL + 14 + meiaPessoa;
      var dir = 14 + meiaPessoa * 2 + espacamentoPessoa * (papeis.length - 1) + 22;
      var largura = CARGO_X_PLANO + internaL + Math.max(56, dir);
      var altura = pisoY + BASE_PLANO;
      var cabH = Math.min(110, Math.max(48, internaA * 0.62));
      var cabY = pisoY - cabH;
      var dados = montarBlocos(carga, veiculo, veiculo.capacidadeKg);
      var visiveis = dados.blocos.filter(function (bloco) {
        return bloco.faixa === 0;
      });
      var svg = [];

      svg.push('<div class="plano-abas" data-plano-abas>');
      svg.push(
        '<div class="plano-abas__botoes" role="tablist" aria-label="Formas de ver o carregamento">' +
          '<button type="button" class="plano-aba plano-aba--ativo" data-plano-aba="3d" role="tab" aria-selected="true"><span aria-hidden="true">🧊</span> 3D</button>' +
          '<button type="button" class="plano-aba" data-plano-aba="lado" role="tab" aria-selected="false"><span aria-hidden="true">📐</span> Lado</button>' +
          '<button type="button" class="plano-aba" data-plano-aba="cima" role="tab" aria-selected="false"><span aria-hidden="true">🗺️</span> Cima</button>' +
          '</div>',
      );
      svg.push(
        '<div class="plano-abas__painel" data-plano-painel="3d">' +
          vistaIsoHtml(dados, veiculo, carga) +
          '<p class="plano-abas__nota">Gire a cabeça, não a tela: a frente do veículo fica em cima e a porta embaixo. Cada cubo é um volume da lista.</p>' +
          '</div>',
      );
      svg.push('<div class="plano-abas__painel" data-plano-painel="lado">');

      svg.push(
        '<svg viewBox="0 0 ' +
          largura.toFixed(1) +
          ' ' +
          altura.toFixed(1) +
          '" class="plano-carga__svg" role="img" aria-label="' +
          (carga.total === 0
            ? 'Caminhão vazio, pronto para receber a mudança'
            : 'Desenho esquemático da carga dentro do veículo') +
          '">',
      );
      svg.push('<title>Plano de carregamento</title>');
      svg.push(
        '<rect x="' +
          (CARGO_X_PLANO - 8) +
          '" y="' +
          (TOPO_PLANO - 8) +
          '" width="' +
          (internaL + 16) +
          '" height="' +
          (internaA + 16) +
          '" rx="12" class="plano-caminhao__parede"/>',
      );
      svg.push(
        '<rect x="' +
          CARGO_X_PLANO +
          '" y="' +
          TOPO_PLANO +
          '" width="' +
          internaL +
          '" height="' +
          internaA +
          '" rx="6" class="plano-caminhao__interior"/>',
      );

      svg.push('<g class="plano-caminhao__cabine">');
      svg.push(
        '<path d="M12 ' +
          pisoY +
          'V' +
          (cabY + 18) +
          'q0-18 18-18h26q14 0 18 13l8 22v' +
          (pisoY - cabY - 35) +
          'H12Z" class="plano-caminhao__cabine-corpo"/>',
      );
      svg.push(
        '<path d="M24 ' +
          (cabY + 18) +
          'h36q8 0 11 8l7 19H24V' +
          (cabY + 18) +
          'Z" class="plano-caminhao__vidro"/>',
      );
      svg.push(
        '<rect x="24" y="' +
          (pisoY - 12) +
          '" width="' +
          (CARGO_X_PLANO - 34) +
          '" height="12" rx="3" class="plano-caminhao__para-choque"/>',
      );
      svg.push(
        '<circle cx="' +
          (CARGO_X_PLANO - 28) +
          '" cy="' +
          (pisoY + 10) +
          '" r="16" class="plano-caminhao__roda"/>',
      );
      svg.push(
        '<circle cx="' +
          (CARGO_X_PLANO - 28) +
          '" cy="' +
          (pisoY + 10) +
          '" r="6" class="plano-caminhao__aro"/>',
      );
      svg.push(
        '<circle cx="' +
          (CARGO_X_PLANO + internaL - 28) +
          '" cy="' +
          (pisoY + 10) +
          '" r="16" class="plano-caminhao__roda"/>',
      );
      svg.push(
        '<circle cx="' +
          (CARGO_X_PLANO + internaL - 28) +
          '" cy="' +
          (pisoY + 10) +
          '" r="6" class="plano-caminhao__aro"/>',
      );
      svg.push('</g>');

      svg.push('<path d="M' + CARGO_X_PLANO + ' ' + pisoY + 'h' + internaL + '" class="plano-caminhao__piso"/>');
      svg.push(
        '<path d="M' +
          (CARGO_X_PLANO + internaL) +
          ' ' +
          TOPO_PLANO +
          'v' +
          internaA +
          '" class="plano-caminhao__porta"/>',
      );
      svg.push(
        '<path d="M' +
          (CARGO_X_PLANO + internaL + 7) +
          ' ' +
          (TOPO_PLANO + 8) +
          'l12 18-12 18" class="plano-caminhao__porta-aberta"/>',
      );

      svg.push('<g class="plano-regua">');
      svg.push('<path d="M' + (CARGO_X_PLANO - 18) + ' ' + TOPO_PLANO + 'v' + internaA + '"/>');
      svg.push('<path d="M' + (CARGO_X_PLANO - 23) + ' ' + TOPO_PLANO + 'h9"/>');
      svg.push('<path d="M' + (CARGO_X_PLANO - 23) + ' ' + (TOPO_PLANO + internaA / 2) + 'h9"/>');
      svg.push('<path d="M' + (CARGO_X_PLANO - 23) + ' ' + pisoY + 'h9"/>');
      svg.push(
        '<text x="' +
          (CARGO_X_PLANO - 27) +
          '" y="' +
          (TOPO_PLANO + 4) +
          '" text-anchor="end">' +
          Math.round(alturaCm) +
          '</text>',
      );
      svg.push(
        '<text x="' +
          (CARGO_X_PLANO - 27) +
          '" y="' +
          (TOPO_PLANO + internaA / 2 + 4) +
          '" text-anchor="end">' +
          Math.round(alturaCm / 2) +
          '</text>',
      );
      svg.push(
        '<text x="' + (CARGO_X_PLANO - 27) + '" y="' + (pisoY + 4) + '" text-anchor="end">0</text>',
      );
      svg.push(
        '<text x="' +
          (CARGO_X_PLANO - 27) +
          '" y="' +
          (TOPO_PLANO - 12) +
          '" text-anchor="end" class="plano-regua__unidade">cm</text>',
      );
      svg.push('</g>');

      if (carga.total === 0) {
        svg.push('<g class="plano-vazio">');
        svg.push(
          '<rect x="' +
            (CARGO_X_PLANO + 22) +
            '" y="' +
            (pisoY - 58) +
            '" width="86" height="58" rx="6" class="plano-vazio__caixa"/>',
        );
        svg.push(
          '<rect x="' +
            (CARGO_X_PLANO + 118) +
            '" y="' +
            (pisoY - 42) +
            '" width="58" height="42" rx="6" class="plano-vazio__caixa"/>',
        );
        svg.push(
          '<text x="' +
            (CARGO_X_PLANO + 26) +
            '" y="' +
            (pisoY - 70) +
            '" class="plano-vazio__texto">escolha os itens para montar o carregamento</text>',
        );
        svg.push('</g>');
      }

      visiveis.forEach(function (bloco) {
        var larguraBloco = Math.max(2, bloco.comprimento * escala - 1);
        var alturaBloco = Math.max(2, bloco.altura * escala - 1);
        var x = CARGO_X_PLANO + bloco.x * escala;
        var y = TOPO_PLANO + (internaA - (bloco.base + bloco.altura) * escala);
        var maxChars = Math.floor((larguraBloco - 6) / 5);
        var rotulo = temBlocoAbaixo(bloco, visiveis)
          ? ''
          : cortarTexto(rotuloCurto(bloco.item.nome), maxChars);
        var fragil = bloco.item.fragil;
        svg.push(
          '<g class="plano-bloco plano-bloco--' +
            classeCategoria(bloco.item.categoria) +
            (fragil ? ' plano-bloco--fragil' : '') +
            '">',
        );
        svg.push(
          '<title>' +
            escaparHtml(bloco.item.nome) +
            (fragil ? ' (frágil)' : '') +
            ' · base a ' +
            Math.round(bloco.base) +
            ' cm · ' +
            Math.round(bloco.altura) +
            ' cm de altura</title>',
        );
        svg.push(
          '<rect x="' +
            x.toFixed(1) +
            '" y="' +
            y.toFixed(1) +
            '" width="' +
            larguraBloco.toFixed(1) +
            '" height="' +
            alturaBloco.toFixed(1) +
            '" rx="3" class="plano-bloco__caixa"/>',
        );
        if (rotulo && alturaBloco >= 16 && larguraBloco >= 30) {
          svg.push(
            '<text x="' +
              (x + 5).toFixed(1) +
              '" y="' +
              (y + Math.min(alturaBloco - 6, 14)).toFixed(1) +
              '" class="plano-bloco__nome">' +
              escaparHtml(rotulo) +
              '</text>',
          );
        }
        if (fragil && larguraBloco >= 42 && alturaBloco >= 26) {
          svg.push(
            '<text x="' +
              (x + larguraBloco - 5).toFixed(1) +
              '" y="' +
              (y + alturaBloco - 6).toFixed(1) +
              '" text-anchor="end" class="plano-bloco__fragil">frágil</text>',
          );
        }
        svg.push('</g>');
      });

      papeis.forEach(function (papel, indice) {
        svg.push(pessoaSvg(papel, pessoaBaseX + indice * espacamentoPessoa, pisoY, escalaPessoa));
      });

      svg.push('</svg>');
      svg.push(
        '<p class="plano-carga__equipe">' +
          (totalAjudantes === 0
            ? 'Sem ajudante: quem organiza a carga é o dono da mudança.'
            : 'Com ' +
              totalAjudantes +
              (totalAjudantes === 1 ? ' ajudante' : ' ajudantes') +
              ': o motorista e a equipe organizam a carga.') +
          '</p>',
      );
      svg.push('</div>');

      svg.push('<div class="plano-abas__painel" data-plano-painel="cima">');
      svg.push(vistaHtml(dados, veiculo));
      svg.push('</div>');
      svg.push('</div>');

      svg.push(categoriasHtml(dados, carga));
      svg.push(viagensHtml(viagens));
      svg.push(listaHtml(dados, viagens));

      return { html: svg.join(''), dados: dados };
    }

    function dicasDoPlano(carga, veiculo) {
      var dados = montarBlocos(carga, veiculo, veiculo.capacidadeKg);
      var nomes = carga.itens
        .map(function (item) {
          return String(item.nome).toLowerCase();
        })
        .join(' ');
      var dicas = ['Comece pelos itens pesados e grandes, rente à frente da carroceria e sempre no chão.'];
      var fragil = carga.itens.some(function (item) {
        return item.fragil;
      });
      if (dados.fora > 0) {
        dicas.push(
          dados.fora +
            (dados.fora === 1 ? ' volume ficou' : ' volumes ficaram') +
            ' fora do desenho. Confirme as medidas ou veja a divisão em viagens no plano.',
        );
      }
      if (fragil) {
        dicas.push('Frágeis por último e por cima, com manta, cantoneira e amarração sem apertar painéis ou portas.');
      }
      if (nomes.indexOf('geladeira') >= 0 || nomes.indexOf('freezer') >= 0) {
        dicas.push('Geladeira em pé e amarrada, longe da porta. Depois da mudança, espere algumas horas antes de ligar.');
      }
      if (nomes.indexOf('colch') >= 0) {
        dicas.push('Colchão em pé ou apoiado em superfície plana. Nunca dobre nem apoie peso em cima.');
      }
      if (nomes.indexOf('tv') >= 0 || nomes.indexOf('espelho') >= 0 || nomes.indexOf('vidro') >= 0) {
        dicas.push('TV, espelhos e vidros na vertical, embalados e presos para não torcerem durante a viagem.');
      }
      if (nomes.indexOf('caixa') >= 0) {
        dicas.push('Numere as caixas por ambiente e deixe as mais leves no topo. Caixas de livros vão no chão.');
      }
      if (dados.volumesAtras > 0) {
        dicas.push(
          'O corte mostra a faixa 1; as faixas de trás completam a largura. Distribua o peso entre os dois lados.',
        );
      }
      if (dados.alturaUsada > veiculo.alturaCm * 0.85) {
        dicas.push('A carga chega perto do teto. Deixe folga para amarração e para a suspensão trabalhar.');
      }
      if (veiculo.capacidadeKg > 0 && carga.pesoKg / veiculo.capacidadeKg > 0.8) {
        dicas.push('Peso elevado: distribua a carga sobre o eixo e confira a tara e a lotação antes de viajar.');
      }
      if (dicas.length === 1 && carga.itens.length > 0) {
        dicas.push('Passe uma cinta ou corda de ponta a ponta e confira a amarração antes de fechar a porta.');
      }
      return dicas.slice(0, 6);
    }

    function atualizarMascote(carga, sugestao) {
      var fala = $('[data-mascote-fala]', montador);
      var detalhe = $('[data-mascote-detalhe]', montador);
      var ajuda = $('[data-mascote-ajuda]', montador);
      if (!fala) return;

      ajuda.classList.remove('mascote-ajuda--atencao', 'mascote-ajuda--ok');
      if (carga.total === 0) {
        fala.textContent = 'Comece pelo que é maior.';
        if (detalhe) {
          detalhe.textContent =
            'Geladeira, cama e sofá definem o veículo. As caixas entram depois, nos espaços que sobram.';
        }
      } else if (sugestao) {
        fala.textContent =
          'Sua mudança tem ' +
          fmtNumero.format(carga.volumeM3) +
          ' m³ e ' +
          fmtPesoCarga.format(Math.round(carga.pesoKg)) +
          ' kg.';
        if (detalhe) {
          detalhe.textContent =
            'O menor veículo que cabe é ' +
            sugestao.veiculo.nome +
            '. O desenho mostra como a carga se organiza.';
        }
        if (sugestao.analise.status === 'limite') ajuda.classList.add('mascote-ajuda--atencao');
        else ajuda.classList.add('mascote-ajuda--ok');
      } else {
        fala.textContent = 'Essa carga passa de uma carreta.';
        if (detalhe) {
          detalhe.textContent =
            'O plano abaixo mostra quantas viagens são necessárias e o que vai em cada uma.';
        }
        ajuda.classList.add('mascote-ajuda--atencao');
      }
    }

    function atualizarNumerosDoPlano(dados, carga, viagens) {
      var numeros = $('[data-plano-numeros]', montador);
      if (!numeros) return;

      if (!dados || carga.total === 0) {
        numeros.hidden = true;
        numeros.textContent = '';
        return;
      }

      var partes = [
        dados.colocados +
          ' de ' +
          carga.total +
          (carga.total === 1 ? ' volume' : ' volumes') +
          (viagens && viagens.totalViagens > 1 ? ' na 1ª viagem' : ' acomodados'),
      ];
      if (dados.volumesAtras > 0) {
        partes.push(
          dados.volumesAtras +
            (dados.volumesAtras === 1 ? ' na faixa de trás' : ' nas faixas de trás'),
        );
      }
      if (viagens && viagens.totalViagens > 1) {
        partes.push(viagens.totalViagens + ' viagens no total');
      } else if (dados.fora > 0) {
        partes.push(dados.fora + ' fora');
      }
      partes.push(fmtNumero.format(dados.volumeColocadoM3 || 0) + ' m³ nesta viagem');
      partes.push(Math.round(dados.alturaUsada) + ' cm de altura usada');
      numeros.textContent = partes.join(' · ');
      numeros.hidden = false;
    }

    function iniciarAbasPlano(raiz) {
      if (!raiz) return;
      var abas = $('[data-plano-abas]', raiz);
      if (!abas) return;

      abas.classList.add('plano-abas--js');
      var botoes = $$('[data-plano-aba]', abas);
      var paineis = $$('[data-plano-painel]', abas);

      function ativar(nome) {
        botoes.forEach(function (botao) {
          var ativo = botao.getAttribute('data-plano-aba') === nome;
          botao.classList.toggle('plano-aba--ativo', ativo);
          botao.setAttribute('aria-selected', ativo ? 'true' : 'false');
        });
        paineis.forEach(function (painel) {
          painel.classList.toggle(
            'plano-abas__painel--ativo',
            painel.getAttribute('data-plano-painel') === nome,
          );
        });
      }

      botoes.forEach(function (botao) {
        botao.addEventListener('click', function () {
          ativar(botao.getAttribute('data-plano-aba'));
        });
      });

      ativar('3d');
    }

    function atualizarPlano(carga, sugestao) {
      var quadro = $('[data-plano-quadro]', montador);
      var dicasEl = $('[data-plano-dicas]', montador);
      var chip = $('[data-plano-veiculo]', montador);
      var tituloEl = $('[data-plano-titulo]', montador);
      if (!quadro) return;

      var veiculo = sugestao
        ? sugestao.veiculo
        : carga.total === 0
          ? veiculosCarga[1] || veiculosCarga[0]
          : veiculosCarga.filter(function (candidato) {
              return candidato.id === 'carreta';
            })[0] || veiculosCarga[veiculosCarga.length - 1];
      if (!veiculo) return;

      var form = montador.closest('form');
      var campoAjudantes = form ? $('input[name="ajudantes"]', form) : null;
      var ajudantes = campoAjudantes ? Math.max(0, Math.floor(Number(campoAjudantes.value) || 0)) : 0;
      var viagens = planejarViagens(carga, veiculo);

      var resultado = svgDoPlano(carga, veiculo, ajudantes, viagens);
      quadro.innerHTML = resultado.html;
      iniciarAbasPlano(quadro);
      atualizarNumerosDoPlano(resultado.dados, carga, viagens);
      if (tituloEl) {
        tituloEl.textContent =
          carga.total === 0 ? 'Seu caminhão, pronto para a mudança' : 'Como a carga se encaixa';
      }
      if (chip) {
        chip.textContent =
          (veiculo.emoji ? veiculo.emoji + ' ' : '') +
          veiculo.nome +
          ' · ' +
          veiculo.comprimentoCm +
          ' × ' +
          veiculo.larguraCm +
          ' × ' +
          veiculo.alturaCm +
          ' cm';
      }

      if (dicasEl) {
        var dicas =
          carga.total === 0
            ? [
                'Monte a lista de itens para o site calcular peso, volume e veículo.',
                'Itens pesados vão na frente e no chão. Frágeis vão por cima.',
                'O desenho é uma prévia: a conferência final é feita no local.',
              ]
            : dicasDoPlano(carga, veiculo);
        var html = '<strong>' + (carga.total === 0 ? 'Antes de carregar' : 'Boas práticas para esta carga') + '</strong><ul>';
        dicas.forEach(function (dica) {
          html += '<li>' + escaparHtml(dica) + '</li>';
        });
        html += '</ul>';
        dicasEl.innerHTML = html;
      }
    }

    function atualizarMontador() {
      var carga = coletarCarga();

      var totalEl = $('[data-carga-total-itens]', montador);
      var pesoEl = $('[data-carga-peso]', montador);
      var volumeEl = $('[data-carga-volume]', montador);
      if (totalEl) totalEl.textContent = String(carga.total);
      if (pesoEl) pesoEl.textContent = fmtPesoCarga.format(Math.round(carga.pesoKg)) + ' kg';
      if (volumeEl) volumeEl.textContent = fmtNumero.format(carga.volumeM3) + ' m³';

      var caixa = $('[data-carga-veiculo-caixa]', montador);
      if (!caixa) return;
      var emoji = $('[data-carga-veiculo-emoji]', caixa);
      var nome = $('[data-carga-veiculo]', caixa);
      var obs = $('[data-carga-veiculo-obs]', caixa);

      caixa.classList.remove(
        'carga-resumo__veiculo--cabe',
        'carga-resumo__veiculo--limite',
        'carga-resumo__veiculo--nao_cabe',
        'carga-resumo__veiculo--vazio'
      );

      var sugestao = recomendarVeiculo(carga);
      if (sugestao) {
        caixa.classList.add('carga-resumo__veiculo--' + sugestao.analise.status);
        if (emoji) emoji.textContent = sugestao.veiculo.emoji;
        if (nome) {
          nome.textContent =
            sugestao.veiculo.nome +
            (sugestao.analise.status === 'limite' ? ' — no limite' : '');
        }
        if (obs) {
          obs.textContent =
            sugestao.veiculo.descricao +
            ' · ' +
            sugestao.veiculo.comprimentoCm +
            ' × ' +
            sugestao.veiculo.larguraCm +
            ' × ' +
            sugestao.veiculo.alturaCm +
            ' cm · até ' +
            fmtPesoCarga.format(sugestao.veiculo.capacidadeKg) +
            ' kg' +
            (sugestao.analise.status === 'limite' ? '. Confirme as medidas: a carga está no limite.' : '');
        }
      } else if (carga.total > 0) {
        caixa.classList.add('carga-resumo__veiculo--nao_cabe');
        if (emoji) emoji.textContent = '⚠️';
        if (nome) nome.textContent = 'Nenhum caminhão padrão';
        if (obs) {
          obs.textContent =
            'A carga não cabe em uma carreta padrão. O plano abaixo mostra quantas viagens são necessárias.';
        }
      } else {
        caixa.classList.add('carga-resumo__veiculo--vazio');
        if (emoji) emoji.textContent = '🚚';
        if (nome) nome.textContent = 'Selecione os itens';
        if (obs) {
          obs.textContent = 'Opcional: monte a carga para estimar peso, volume e veículo.';
        }
      }

      atualizarMascote(carga, sugestao);
      atualizarPlano(carga, sugestao);
    }

    $$('[data-item-carga]', montador).forEach(function (linha) {
      var campo = $('[data-qtd]', linha);

      $$('[data-stepper]', linha).forEach(function (botao) {
        botao.addEventListener('click', function () {
          var atual = quantidadeDa(linha);
          var delta = botao.getAttribute('data-stepper') === 'mais' ? 1 : -1;
          var proximo = Math.max(0, Math.min(20, atual + delta));
          if (campo) {
            campo.value = proximo > 0 ? String(proximo) : '';
            campo.dispatchEvent(new Event('input', { bubbles: true }));
          }
        });
      });

      if (!campo) return;
      campo.addEventListener('input', function () {
        var numero = Math.floor(Number(String(campo.value).replace(',', '.')));
        if (isFinite(numero)) {
          if (numero > 20) campo.value = '20';
          if (numero < 0) campo.value = '0';
        }
        atualizarMontador();
      });
      campo.addEventListener('blur', function () {
        var numero = Math.floor(Number(String(campo.value).replace(',', '.')));
        campo.value = !isFinite(numero) || numero <= 0 ? '' : String(Math.min(20, numero));
        atualizarMontador();
      });
    });

    var campoCaixasQtd = $('[data-caixas-qtd]', montador);
    var selectCaixasTamanho = $('[data-caixas-tamanho]', montador);
    var blocoMedidasCaixas = $('[data-caixas-medidas]', montador);
    function atualizarMedidasCaixas() {
      if (!selectCaixasTamanho || !blocoMedidasCaixas) return;
      blocoMedidasCaixas.hidden = selectCaixasTamanho.value !== 'personalizada';
    }

    var caixasWrap = $('[data-caixas]', montador);
    if (caixasWrap) {
      $$('input, select', caixasWrap).forEach(function (campo) {
        campo.addEventListener('input', atualizarMontador);
        campo.addEventListener('change', function () {
          atualizarMedidasCaixas();
          atualizarMontador();
        });
      });
    }
    if (campoCaixasQtd) {
      campoCaixasQtd.addEventListener('blur', function () {
        var numero = Math.floor(Number(String(campoCaixasQtd.value).replace(',', '.')));
        if (!isFinite(numero) || numero <= 0) campoCaixasQtd.value = '';
        else if (numero > 200) campoCaixasQtd.value = '200';
        atualizarMontador();
      });
    }

    var formMontador = montador.closest('form');
    var campoAjudantesMontador = formMontador ? $('input[name="ajudantes"]', formMontador) : null;
    if (campoAjudantesMontador) {
      campoAjudantesMontador.addEventListener('input', atualizarMontador);
      campoAjudantesMontador.addEventListener('change', atualizarMontador);
    }

    atualizarMedidasCaixas();
    atualizarMontador();
  }

  // ---------- estimador de custo (exclusivo do transportador) ----------
  var estimador = $('[data-estimador]');
  if (estimador) {
    var CHAVE_CUSTO = 'frete:custo:v2';
    var iniciais = null;
    try {
      iniciais = JSON.parse(estimador.getAttribute('data-inicial') || 'null');
    } catch (e) {
      iniciais = null;
    }

    var referencias = null;
    try {
      referencias = JSON.parse(estimador.getAttribute('data-referencias') || 'null');
    } catch (e) {
      referencias = null;
    }

    var distanciaRodoviaria = Number(estimador.getAttribute('data-distancia'));
    if (!isFinite(distanciaRodoviaria) || distanciaRodoviaria <= 0) distanciaRodoviaria = 0;

    var MERCADO_PADRAO = {
      fiorino: { rotulo: 'Fiorino', piso: 250, localMaximo: 450, porKmMin: 3.5, porKmMax: 5.5 },
      van: { rotulo: 'Van', piso: 350, localMaximo: 650, porKmMin: 4, porKmMax: 6.5 },
      tres_quartos: { rotulo: 'Caminhão 3/4', piso: 450, localMaximo: 850, porKmMin: 5, porKmMax: 8.5 },
      bau: { rotulo: 'Caminhão baú', piso: 650, localMaximo: 1200, porKmMin: 7, porKmMax: 11 },
      carreta: { rotulo: 'Carreta', piso: 900, localMaximo: 1800, porKmMin: 9, porKmMax: 14 },
      outro: { rotulo: 'Outro veículo', piso: 300, localMaximo: 800, porKmMin: 4, porKmMax: 8 },
    };
    var COMBUSTIVEL_PADRAO = { diesel: 7.37, gasolina: 6.09 };

    function mercadoAtual() {
      return (referencias && referencias.mercado) || MERCADO_PADRAO;
    }

    function combustivelAtual() {
      return (referencias && referencias.combustivel) || COMBUSTIVEL_PADRAO;
    }

    function taxasAtuais() {
      return (referencias && referencias.taxaBasePorTipo) || {
        fiorino: 120,
        van: 180,
        tres_quartos: 250,
        bau: 400,
        carreta: 600,
        outro: 180,
      };
    }

    var padroes = {
      consumoKmL: 8,
      precoCombustivel: COMBUSTIVEL_PADRAO.diesel,
      pedagioPorKm: 0.1,
      manutencaoPorKm: 0.45,
      arlaPorKm: 0.06,
      custoFixo: 0,
      margemPercentual: 20,
      taxaBase: 180,
      horasServico: 2,
      custoHora: 45,
      custoAjudante: 120,
      ajudantes: 0,
      tipoCombustivel: 'diesel',
      tipoVeiculo: 'van',
    };

    function numeroDoCampo(valor, padrao, minimo) {
      var texto = String(valor == null ? '' : valor).trim();
      // Campo vazio (ou só espaços) volta ao padrão — zero só quando foi
      // digitado de propósito.
      if (texto === '') return padrao;
      var n = Number(texto.replace(',', '.'));
      if (!isFinite(n) || n < (minimo == null ? 0 : minimo)) return padrao;
      return n;
    }

    function arredondarMoeda(v) {
      return Math.round(v * 100) / 100;
    }

    function arredondarDez(v) {
      return Math.ceil(v / 10) * 10;
    }

    function lerParametros() {
      var p = {};
      Object.keys(padroes).forEach(function (chave) {
        var campo = $('[data-custo="' + chave + '"]', estimador);
        if (!campo) {
          p[chave] = padroes[chave];
          return;
        }
        if (chave === 'tipoCombustivel') {
          p[chave] = campo.value === 'gasolina' ? 'gasolina' : 'diesel';
        } else if (chave === 'tipoVeiculo') {
          p[chave] = campo.value || 'van';
        } else {
          var minimo =
            chave === 'consumoKmL'
              ? 1
              : chave === 'precoCombustivel'
                ? 0.01
                : chave === 'margemPercentual'
                  ? 0
                  : 0;
          p[chave] = numeroDoCampo(campo.value, padroes[chave], minimo);
        }
      });

      p.margemPercentual = Math.min(100, p.margemPercentual);
      p.ajudantes = Math.max(0, Math.min(20, Math.round(p.ajudantes)));
      // Gasolina não usa ARLA 32: zera para não cobrar um insumo que não existe.
      if (p.tipoCombustivel !== 'diesel') p.arlaPorKm = 0;
      return p;
    }

    function calcular(p) {
      var distancia = Math.round(distanciaRodoviaria * 10) / 10;
      var litros = p.consumoKmL > 0 ? distancia / p.consumoKmL : 0;
      var combustivel = litros * p.precoCombustivel;
      var pedagio = distancia * p.pedagioPorKm;
      var manutencao = distancia * p.manutencaoPorKm;
      var arla = p.tipoCombustivel === 'diesel' ? distancia * p.arlaPorKm : 0;
      var custoHoras = p.horasServico * p.custoHora;
      var custoAjudantes = p.ajudantes * p.custoAjudante;
      var custoTotal =
        combustivel +
        pedagio +
        manutencao +
        arla +
        p.taxaBase +
        custoHoras +
        custoAjudantes +
        p.custoFixo;
      var margem = custoTotal * (p.margemPercentual / 100);

      var base = mercadoAtual()[p.tipoVeiculo] || mercadoAtual().van;
      var pisoMercado = arredondarDez(Math.max(base.piso, distancia * base.porKmMin));
      var tetoMercado = arredondarDez(
        Math.max(base.localMaximo, distancia * base.porKmMax, pisoMercado + 100)
      );
      var precoCusto = arredondarDez(custoTotal + margem);
      var precoSugerido = Math.max(precoCusto, pisoMercado);

      return {
        distanciaRodoviariaKm: distancia,
        combustivel: arredondarMoeda(combustivel),
        pedagio: arredondarMoeda(pedagio),
        manutencao: arredondarMoeda(manutencao + arla),
        taxaBase: arredondarMoeda(p.taxaBase),
        custoHoras: arredondarMoeda(custoHoras),
        custoAjudantes: arredondarMoeda(custoAjudantes),
        custoFixo: arredondarMoeda(p.custoFixo),
        custoTotal: arredondarMoeda(custoTotal),
        margem: arredondarMoeda(margem),
        custoPorKm: distancia > 0 ? arredondarMoeda(custoTotal / distancia) : 0,
        precoMinimo: arredondarDez(custoTotal),
        pisoMercado: pisoMercado,
        tetoMercado: tetoMercado,
        precoSugerido: precoSugerido,
        abaixoDoMercado: precoCusto < pisoMercado,
      };
    }

    function textoMercado(resultado, tipo) {
      var base = mercadoAtual()[tipo] || mercadoAtual().van;
      return (
        'Mercado (' +
        base.rotulo +
        '): ' +
        fmtMoeda.format(resultado.pisoMercado) +
        ' a ' +
        fmtMoeda.format(resultado.tetoMercado)
      );
    }

    function atualizarCombustivelUI() {
      var seletor = $('[data-custo="tipoCombustivel"]', estimador);
      var tipo = seletor && seletor.value === 'gasolina' ? 'gasolina' : 'diesel';

      var referencia = $('[data-combustivel-referencia-texto]', estimador);
      if (referencia) referencia.textContent = fmtMoeda.format(combustivelAtual()[tipo] || 0);
    }

    function atualizarEstimador() {
      var parametros = lerParametros();
      var resultado = calcular(parametros);
      $$('[data-saida]', estimador).forEach(function (saida) {
        var chave = saida.getAttribute('data-saida');
        if (chave === 'distanciaRodoviariaKm') {
          saida.textContent = fmtNumero.format(resultado.distanciaRodoviariaKm) + ' km';
        } else if (chave === 'faixaMercado') {
          saida.textContent = textoMercado(resultado, parametros.tipoVeiculo);
        } else if (Object.prototype.hasOwnProperty.call(resultado, chave)) {
          saida.textContent = fmtMoeda.format(resultado[chave]);
        }
      });

      var aviso = $('[data-aviso-mercado]', estimador);
      if (aviso) aviso.hidden = !resultado.abaixoDoMercado;

      var sugestao = $('[data-usar-sugestao]', estimador);
      if (sugestao) sugestao.setAttribute('data-valor-sugerido', String(resultado.precoSugerido));

      atualizarCombustivelUI();
    }

    function salvarParametros() {
      try {
        var dados = {};
        $$('[data-custo]', estimador).forEach(function (campo) {
          dados[campo.getAttribute('data-custo')] = campo.value;
        });
        window.localStorage.setItem(CHAVE_CUSTO, JSON.stringify(dados));
      } catch (e) {
        /* modo privado: segue sem salvar */
      }
    }

    try {
      var salvos = JSON.parse(window.localStorage.getItem(CHAVE_CUSTO) || '{}');
      var fonte = iniciais && typeof iniciais === 'object' ? iniciais : salvos;
      $$('[data-custo]', estimador).forEach(function (campo) {
        var chave = campo.getAttribute('data-custo');
        if (fonte && fonte[chave] != null && fonte[chave] !== '') campo.value = fonte[chave];
      });
    } catch (e) {
      /* dados antigos inválidos: usa os padrões */
    }

    $$('[data-custo]', estimador).forEach(function (campo) {
      campo.addEventListener('input', function () {
        atualizarEstimador();
        salvarParametros();
      });
      campo.addEventListener('change', function () {
        atualizarEstimador();
        salvarParametros();
      });
    });

    var seletorVeiculo = $('[data-custo="tipoVeiculo"]', estimador);
    if (seletorVeiculo) {
      seletorVeiculo.addEventListener('change', function () {
        var tipo = seletorVeiculo.value;
        var taxa = taxasAtuais()[tipo];
        var campoTaxa = $('[data-custo="taxaBase"]', estimador);
        if (taxa != null && campoTaxa) campoTaxa.value = String(taxa);
        var dica = $('[data-taxa-sugestao]', estimador);
        if (dica && taxa != null) {
          var opcao = seletorVeiculo.options[seletorVeiculo.selectedIndex];
          var rotulo = opcao ? opcao.textContent.split(' — ')[0] : tipo;
          dica.textContent =
            'Sugestão para ' + rotulo.toLowerCase() + ': ' + fmtMoeda.format(taxa) + '.';
        }
        // O combustível acompanha o veículo; o transportador pode trocar.
        var seletorCombustivel = $('[data-custo="tipoCombustivel"]', estimador);
        var combustivelSugerido = tipo === 'fiorino' ? 'gasolina' : 'diesel';
        if (seletorCombustivel) seletorCombustivel.value = combustivelSugerido;
        var campoPreco = $('[data-custo="precoCombustivel"]', estimador);
        if (campoPreco) {
          campoPreco.value = String(combustivelAtual()[combustivelSugerido]);
        }
        atualizarEstimador();
        salvarParametros();
      });
    }

    var botaoReferencia = $('[data-combustivel-referencia]', estimador);
    if (botaoReferencia) {
      botaoReferencia.addEventListener('click', function () {
        var seletor = $('[data-custo="tipoCombustivel"]', estimador);
        var tipo = seletor && seletor.value === 'gasolina' ? 'gasolina' : 'diesel';
        var preco = $('[data-custo="precoCombustivel"]', estimador);
        if (preco) preco.value = String(combustivelAtual()[tipo]);
        atualizarEstimador();
        salvarParametros();
      });
    }

    var botaoSugestao = $('[data-usar-sugestao]', estimador);
    if (botaoSugestao) {
      botaoSugestao.addEventListener('click', function () {
        var valor = Number(botaoSugestao.getAttribute('data-valor-sugerido') || 0);
        var campoLance = $('[data-lance-valor]');
        if (!valor || !campoLance) return;
        campoLance.value = valor.toLocaleString('pt-BR', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
        campoLance.dispatchEvent(new Event('input', { bubbles: true }));
        campoLance.focus();
        campoLance.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
    }

    // Sem referências embutidas (ex.: página antiga em cache), busca a API.
    if (!referencias) {
      fetch('/api/referencias', { headers: { Accept: 'application/json' } })
        .then(function (resposta) {
          return resposta.ok ? resposta.json() : null;
        })
        .then(function (dados) {
          if (!dados || !dados.combustivel) return;
          referencias = dados;
          atualizarEstimador();
        })
        .catch(function () {
          /* segue com os padrões locais */
        });
    }

    atualizarEstimador();
  }

  // ---------- IA: melhorar descrição / mensagem ----------
  $$('[data-ia]').forEach(function (botao) {
    botao.addEventListener('click', function () {
      var tipo = botao.getAttribute('data-ia');
      var alvo = document.getElementById(botao.getAttribute('data-alvo') || '');
      if (!alvo) return;

      var formulario = botao.closest('form');
      var status = formulario ? $('[data-ia-status]', formulario) : null;
      var texto = String(alvo.value || '').trim();

      function avisar(mensagem, erro) {
        if (!status) return;
        status.textContent = mensagem;
        status.classList.toggle('campo__dica--erro', Boolean(erro));
        status.classList.toggle('campo__dica--ok', !erro && Boolean(mensagem));
      }

      if (texto.length < 5) {
        avisar('Escreva pelo menos uma frase antes de usar a IA.', true);
        alvo.focus();
        return;
      }
      if (alvo.value.trim() && !window.confirm('Substituir o texto atual pelo texto sugerido pela IA?')) {
        return;
      }

      var rotuloOriginal = botao.textContent;
      botao.disabled = true;
      botao.textContent = '✨ Melhorando…';
      avisar('', false);

      fetch('/api/ia/texto', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ texto: texto, tipo: tipo }),
      })
        .then(function (resposta) {
          return resposta.json().then(function (dados) {
            if (!resposta.ok) throw new Error(dados && dados.erro ? dados.erro : 'Não foi possível usar a IA agora.');
            return dados;
          });
        })
        .then(function (dados) {
          if (!dados || !dados.texto) throw new Error('A IA não retornou um texto.');
          alvo.value = dados.texto;
          alvo.dispatchEvent(new Event('input', { bubbles: true }));
          alvo.focus();
          avisar('Texto melhorado pela IA. Revise antes de enviar.', false);
        })
        .catch(function (erro) {
          avisar(erro.message || 'Não foi possível usar a IA agora. Tente de novo.', true);
        })
        .finally(function () {
          botao.disabled = false;
          botao.textContent = rotuloOriginal;
        });
    });
  });

  // ---------- IA: análise da carga com o caminhão do transportador ----------
  $$('[data-ia-carga]').forEach(function (botao) {
    botao.addEventListener('click', function () {
      var caixa = botao.closest('.ia-carga');
      var status = caixa ? $('[data-ia-carga-status]', caixa) : null;
      var saida = caixa ? $('[data-ia-carga-saida]', caixa) : null;
      var freteId = Number(botao.getAttribute('data-frete-id'));

      function avisar(mensagem, erro) {
        if (!status) return;
        status.textContent = mensagem;
        status.classList.toggle('campo__dica--erro', Boolean(erro));
        status.classList.toggle('campo__dica--ok', !erro && Boolean(mensagem));
      }

      if (!freteId) {
        avisar('Não foi possível identificar o frete.', true);
        return;
      }

      var rotuloOriginal = botao.textContent;
      botao.disabled = true;
      botao.textContent = '✨ Analisando…';
      avisar('A IA está conferindo a carga e os custos…', false);

      fetch('/api/ia/carga', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ freight_id: freteId }),
      })
        .then(function (resposta) {
          return resposta.json().then(function (dados) {
            if (!resposta.ok) {
              throw new Error(dados && dados.erro ? dados.erro : 'Não foi possível analisar agora.');
            }
            return dados;
          });
        })
        .then(function (dados) {
          if (!dados || !dados.texto) throw new Error('A IA não retornou uma análise.');
          if (saida) {
            saida.textContent = dados.texto;
            saida.hidden = false;
          }
          avisar('Análise gerada pela IA. A conferência de encaixe acima é a que vale.', false);
        })
        .catch(function (erro) {
          avisar(erro.message || 'Não foi possível usar a IA agora. Tente de novo.', true);
        })
        .finally(function () {
          botao.disabled = false;
          botao.textContent = rotuloOriginal;
        });
    });
  });
  // ---------- tour de primeiro acesso e ajuda fixa ----------
  var CHAVE_TOUR = 'qm-tour-v1';
  var tour = $('[data-tour]');
  var tourCaixa = tour ? $('[data-tour-caixa]', tour) : null;
  var ajuda = $('[data-ajuda-fixa]');
  var ajudaPainel = ajuda ? $('#ajuda-painel') : null;
  var ajudaBotao = ajuda ? $('.ajuda-fixa__botao', ajuda) : null;
  var ultimoFocoTour = null;
  var passoAtual = 0;

  function lerTourVisto() {
    try {
      return window.localStorage.getItem(CHAVE_TOUR) === '1';
    } catch (erro) {
      return false;
    }
  }

  function marcarTourVisto() {
    try {
      window.localStorage.setItem(CHAVE_TOUR, '1');
    } catch (erro) {
      // Sem localStorage (modo privado/bloqueado), o tour só não é lembrado.
    }
  }

  function passosDoTour() {
    return tour ? $$('[data-tour-passo]', tour) : [];
  }

  function pontosDoTour() {
    return tour ? $$('[data-tour-ponto]', tour) : [];
  }

  function focaveisDoTour() {
    if (!tourCaixa) return [];
    return $$('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])', tourCaixa).filter(
      function (elemento) {
        return !elemento.hidden && elemento.offsetParent !== null;
      },
    );
  }

  function mostrarPassoTour(indice) {
    var passos = passosDoTour();
    if (!passos.length) return;

    passoAtual = Math.max(0, Math.min(passos.length - 1, indice));
    passos.forEach(function (passo, i) {
      passo.hidden = i !== passoAtual;
    });

    var atual = passos[passoAtual];
    var fala = tour ? $('[data-tour-fala]', tour) : null;
    if (fala && atual) fala.textContent = atual.getAttribute('data-tour-texto') || '';

    var contador = tour ? $('[data-tour-contador]', tour) : null;
    if (contador) contador.textContent = passoAtual + 1 + ' de ' + passos.length;

    pontosDoTour().forEach(function (ponto, i) {
      ponto.classList.toggle('tour__ponto--ativo', i === passoAtual);
      if (i === passoAtual) ponto.setAttribute('aria-current', 'step');
      else ponto.removeAttribute('aria-current');
    });

    var voltar = tour ? $('[data-tour-voltar]', tour) : null;
    var continuar = tour ? $('[data-tour-continuar]', tour) : null;
    if (voltar) voltar.disabled = passoAtual === 0;
    if (continuar) {
      continuar.textContent = passoAtual === passos.length - 1 ? 'Concluir' : 'Continuar';
    }
  }

  function abrirTour(forcar) {
    if (!tour) return;
    if (!forcar && lerTourVisto()) return;

    ultimoFocoTour = document.activeElement;
    tour.hidden = false;
    document.body.classList.add('tour-aberto');
    marcarTourVisto();
    mostrarPassoTour(0);

    if (tourCaixa) {
      window.setTimeout(function () {
        tourCaixa.focus();
      }, 30);
    }
  }

  function fecharTour() {
    if (!tour || tour.hidden) return;
    tour.hidden = true;
    document.body.classList.remove('tour-aberto');
    marcarTourVisto();

    if (ultimoFocoTour && typeof ultimoFocoTour.focus === 'function') {
      ultimoFocoTour.focus();
    }
  }

  function abrirAjuda() {
    if (!ajudaPainel || !ajudaBotao) return;
    ajudaPainel.hidden = false;
    ajudaBotao.setAttribute('aria-expanded', 'true');
    var primeiro = ajudaPainel.querySelector('a, button');
    if (primeiro) primeiro.focus();
  }

  function fecharAjuda(devolverFoco) {
    if (!ajudaPainel || !ajudaBotao) return;
    if (ajudaPainel.hidden) return;
    ajudaPainel.hidden = true;
    ajudaBotao.setAttribute('aria-expanded', 'false');
    if (devolverFoco) ajudaBotao.focus();
  }

  if (tour) {
    mostrarPassoTour(0);

    $$('[data-tour-fechar]', tour).forEach(function (botao) {
      botao.addEventListener('click', function () {
        fecharTour();
      });
    });

    var botaoContinuar = $('[data-tour-continuar]', tour);
    if (botaoContinuar) {
      botaoContinuar.addEventListener('click', function () {
        var passos = passosDoTour();
        if (passoAtual >= passos.length - 1) fecharTour();
        else mostrarPassoTour(passoAtual + 1);
      });
    }

    var botaoVoltar = $('[data-tour-voltar]', tour);
    if (botaoVoltar) {
      botaoVoltar.addEventListener('click', function () {
        mostrarPassoTour(passoAtual - 1);
      });
    }

    var botaoPular = $('[data-tour-pular]', tour);
    if (botaoPular) {
      botaoPular.addEventListener('click', function () {
        fecharTour();
      });
    }

    pontosDoTour().forEach(function (ponto) {
      ponto.addEventListener('click', function () {
        mostrarPassoTour(Number(ponto.getAttribute('data-tour-ponto')) || 0);
      });
    });

    if (tour.getAttribute('data-tour-auto') === '1' && !lerTourVisto()) {
      window.setTimeout(function () {
        abrirTour(false);
      }, 650);
    }
  }

  if (ajuda) {
    if (ajudaBotao) {
      ajudaBotao.addEventListener('click', function () {
        if (ajudaPainel && ajudaPainel.hidden) abrirAjuda();
        else fecharAjuda(true);
      });
    }

    var ajudaFechar = $('.ajuda-fixa__fechar', ajuda);
    if (ajudaFechar) {
      ajudaFechar.addEventListener('click', function () {
        fecharAjuda(true);
      });
    }

    $$('[data-tour-abrir]', ajuda).forEach(function (botao) {
      botao.addEventListener('click', function () {
        fecharAjuda(false);
        abrirTour(true);
      });
    });

    document.addEventListener('click', function (evento) {
      if (!ajudaPainel || ajudaPainel.hidden) return;
      if (!ajuda.contains(evento.target)) fecharAjuda(false);
    });
  }

  document.addEventListener('keydown', function (evento) {
    if (tour && !tour.hidden) {
      if (evento.key === 'Escape') {
        evento.preventDefault();
        fecharTour();
        return;
      }
      if (evento.key === 'ArrowRight') {
        evento.preventDefault();
        if (passoAtual < passosDoTour().length - 1) mostrarPassoTour(passoAtual + 1);
        return;
      }
      if (evento.key === 'ArrowLeft') {
        evento.preventDefault();
        mostrarPassoTour(passoAtual - 1);
        return;
      }
      if (evento.key === 'Tab') {
        var focaveis = focaveisDoTour();
        if (!focaveis.length) return;
        var primeiro = focaveis[0];
        var ultimo = focaveis[focaveis.length - 1];
        if (!tourCaixa.contains(document.activeElement)) {
          evento.preventDefault();
          primeiro.focus();
        } else if (evento.shiftKey && document.activeElement === primeiro) {
          evento.preventDefault();
          ultimo.focus();
        } else if (!evento.shiftKey && document.activeElement === ultimo) {
          evento.preventDefault();
          primeiro.focus();
        }
      }
      return;
    }

    if (evento.key === 'Escape') fecharAjuda(false);
  });
})();
