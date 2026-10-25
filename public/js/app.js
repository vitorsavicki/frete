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
  var camadaForm = null;
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
      camadaForm = L.layerGroup().addTo(mapaForm);
    }

    camadaForm.clearLayers();
    var pontos = [];

    if (origem) {
      L.marker([origem.lat, origem.lng]).addTo(camadaForm).bindPopup('Origem');
      pontos.push([origem.lat, origem.lng]);
    }
    if (destino) {
      L.marker([destino.lat, destino.lng]).addTo(camadaForm).bindPopup('Destino');
      pontos.push([destino.lat, destino.lng]);
    }
    if (origem && destino) {
      L.polyline(
        [
          [origem.lat, origem.lng],
          [destino.lat, destino.lng],
        ],
        { color: '#1d4ed8', weight: 3, dashArray: '7 8', opacity: 0.85 }
      ).addTo(camadaForm);
      mapaForm.fitBounds(L.latLngBounds(pontos), { padding: [30, 30], maxZoom: 12 });
      var distancia = Math.round(haversine(origem.lat, origem.lng, destino.lat, destino.lng) * 1.25);
      if (statusMapaForm) {
        statusMapaForm.textContent =
          'Prévia da rota: aproximadamente ' + fmtNumero.format(distancia) + ' km por estrada.';
      }
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

    function quantidadeDa(linha) {
      var campo = $('[data-qtd]', linha);
      var n = campo ? Math.floor(Number(String(campo.value).replace(',', '.'))) : 0;
      if (!isFinite(n) || n < 0) n = 0;
      return Math.min(20, n);
    }

    function coletarCarga() {
      var itens = [];
      var total = 0;
      $$('[data-item-carga]', montador).forEach(function (linha) {
        var quantidade = quantidadeDa(linha);
        if (quantidade <= 0) return;
        var restante = 200 - total;
        if (restante <= 0) return;
        quantidade = Math.min(quantidade, restante);
        total += quantidade;
        itens.push({
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
      var noLimite = null;
      for (var i = 0; i < veiculosCarga.length; i++) {
        var analise = analisarComVeiculo(carga, veiculosCarga[i]);
        if (!analise) continue;
        if (analise.status === 'cabe') return { veiculo: veiculosCarga[i], analise: analise };
        if (analise.status === 'limite' && !noLimite) {
          noLimite = { veiculo: veiculosCarga[i], analise: analise };
        }
      }
      return noLimite;
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
            'A carga passa de uma carreta padrão — provavelmente serão necessárias duas viagens.';
        }
      } else {
        caixa.classList.add('carga-resumo__veiculo--vazio');
        if (emoji) emoji.textContent = '🚚';
        if (nome) nome.textContent = 'Selecione os itens';
        if (obs) {
          obs.textContent = 'Opcional: monte a carga para estimar peso, volume e veículo.';
        }
      }
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

    atualizarMontador();
  }

  // ---------- estimador de custo ----------
  var estimador = $('[data-estimador]');
  if (estimador) {
    var CHAVE_CUSTO = 'frete:custo:v1';
    var iniciais = null;
    try {
      iniciais = JSON.parse(estimador.getAttribute('data-inicial') || 'null');
    } catch (e) {
      iniciais = null;
    }
    var distanciaLinhaReta = Number(estimador.getAttribute('data-distancia'));
    var padroes = {
      consumoKmL: 8,
      precoDiesel: 6.3,
      pedagioPorKm: 0.1,
      manutencaoPorKm: 0.35,
      arlaPorKm: 0.05,
      custoFixo: 0,
      margemPercentual: 20,
      fatorRodovia: 1.25,
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

    function lerParametros() {
      var p = {};
      Object.keys(padroes).forEach(function (chave) {
        var campo = $('[data-custo="' + chave + '"]', estimador);
        p[chave] = numeroDoCampo(campo ? campo.value : padroes[chave], padroes[chave], chave === 'consumoKmL' ? 1 : chave === 'precoDiesel' ? 0.01 : chave === 'fatorRodovia' ? 1 : 0);
      });
      p.margemPercentual = Math.min(100, p.margemPercentual);
      return p;
    }

    function calcular(p) {
      var distanciaRodoviariaKm = Math.round(distanciaLinhaReta * p.fatorRodovia * 10) / 10;
      var litros = distanciaRodoviariaKm / p.consumoKmL;
      var combustivel = litros * p.precoDiesel;
      var pedagio = distanciaRodoviariaKm * p.pedagioPorKm;
      var manutencao = distanciaRodoviariaKm * p.manutencaoPorKm;
      var arla = distanciaRodoviariaKm * p.arlaPorKm;
      var custoTotal = combustivel + pedagio + manutencao + arla + p.custoFixo;
      var margem = custoTotal * (p.margemPercentual / 100);
      var arredondar = function (v) {
        return Math.round(v * 100) / 100;
      };
      return {
        distanciaRodoviariaKm: distanciaRodoviariaKm,
        combustivel: arredondar(combustivel),
        pedagio: arredondar(pedagio),
        manutencao: arredondar(manutencao),
        arla: arredondar(arla),
        custoFixo: arredondar(p.custoFixo),
        custoTotal: arredondar(custoTotal),
        margem: arredondar(margem),
        precoMinimo: Math.ceil(custoTotal / 10) * 10,
        precoSugerido: Math.ceil((custoTotal + margem) / 10) * 10,
      };
    }

    function atualizarEstimador() {
      var resultado = calcular(lerParametros());
      $$('[data-saida]', estimador).forEach(function (saida) {
        var chave = saida.getAttribute('data-saida');
        if (chave === 'distanciaRodoviariaKm') {
          saida.textContent = fmtNumero.format(resultado.distanciaRodoviariaKm) + ' km';
        } else if (Object.prototype.hasOwnProperty.call(resultado, chave)) {
          saida.textContent = fmtMoeda.format(resultado[chave]);
        }
      });
    }

    function salvarParametros() {
      if (iniciais) return;
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
      campo.addEventListener('change', salvarParametros);
    });

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
})();
