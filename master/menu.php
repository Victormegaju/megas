<div class="slim-body">
  <?php
  $urlk = $_SERVER["REQUEST_URI"];

  $currentPage = basename($urlk);


  $url_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

  
  $dataExpiracaoBr = date('d/m/Y', strtotime($dataExpiracao)); 
  

  $menuItems = [
    ["name" => "Home", "path" => "./", "icon" => $url_base . "/master/icon/controle.png", "activeOn" => ["master"]],
    ["name" => "Grupo/Categoria", "path" => "categorias", "icon" => $url_base . "/master/icon/categorizar.png", "activeOn" => ["categorias", "cad_categoria", "edit_categoria"]],
    ["name" => "Clientes", "path" => "clientes", "icon" => $url_base . "/master/icon/clientes.png", "activeOn" => ["clientes", "ver_cliente", "cad_cliente", "edit_cliente"]],
    ["name" => "Contas a Receber", "path" => "contas_receber", "icon" => $url_base . "/master/icon/fatura.png", "activeOn" => ["cad_contas", "contas_receber", "editar_mensalidade", "cad_contas_simulador", "ver_financeiro"]],
    ["name" => "Contas a Pagar", "path" => "contas_pagar", "icon" =>  $url_base . "/master/icon/verificar.png", "activeOn" => ["contas_pagar", "cad_pagar", "editar_pagamento", "cad_pagar_simulador"]],
    ["name" => "Extrato de Pagamento", "path" => "finalizados", "icon" =>  $url_base . "/master/icon/pagamento.png", "activeOn" => ["finalizados", "ver_financeiro_quitado"]],
    ["name" => "Notificações", "path" => "mensagens", "icon" =>  $url_base . "/master/icon/notificacoes.png", "activeOn" => ["mensagens", "edit_mensagens"]],
  ];

  if ($dadosgerais->tipo == 1) {
    $menuItems[] = ["name" => "Usuários SAAS", "path" => "usuarios", "icon" =>  $url_base . "/master/icon/usuarios.png", "activeOn" => ["usuarios", "cad_usuario", "edit_usuario", "painel_usuario"]];
  }

  $menuItems[] = ["name" => "Planos", "path" => "planos", "icon" =>  $url_base . "/master/icon/planos.png", "activeOn" => ["planos", "cad_planos", "edit_planos"]];

  $menuItems[] = ["name" => "Meu Perfil", "path" => "perfil", "icon" => $url_base . "/master/icon/profile.png", "activeOn" => ["perfil"]];
  $menuItems[] = ["name" => "Configurações", "path" => "configuracoes", "icon" => $url_base . "/master/icon/configurar.png", "activeOn" => ["configuracoes"]];
  $menuItems[] = ["name" => "WhatsApp", "path" => "whatsapp", "icon" => $url_base . "/master/icon/whatsapp.png", "activeOn" => ["whatsapp"]];
  $menuItems[] = ["name" => "MercadoPago", "path" => "mercadopago", "icon" => $url_base . "/master/icon/mercado.png", "activeOn" => ["mercadopago"]];
  $menuItems[] = ["name" => "Tutoriais", "path" => "tutoriais", "icon" => $url_base . "/master/icon/tutorial.png", "activeOn" => ["tutoriais"]];
  $menuItems[] = ["name" => "Log de Atualizações", "path" => "update", "icon" => $url_base . "/master/icon/update.png", "activeOn" => ["update"]];
  $menuItems[] = ["name" => "Sair", "path" => "sair", "icon" => $url_base . "/master/icon/sair.png", "activeOn" => ["sair"]];


  ?>

  <div class="slim-sidebar">
      <?php if ($dadosgerais->tipo > 1) { ?>
        <div id="msg-assinatura" class="slim-header-center mobile">
          <div class="time">Seu plano expira em: <span class="expiration-date"><?php echo date('d/m/Y', strtotime($dataExpiracao)); ?></span></div>
        </div>
      <?php } ?>

    <label class="sidebar-label">MENU</label>

    <ul class="nav nav-sidebar">
      <?php foreach ($menuItems as $item): ?>
        <li class="sidebar-nav-item">
          <a href="<?php echo $item["path"]; ?>"
            class="sidebar-nav-link <?php if (in_array($currentPage, $item["activeOn"])) {
              echo "active";
            } ?>">
            <?php if (filter_var($item["icon"], FILTER_VALIDATE_URL)): ?>
              <img src="<?php echo $item["icon"]; ?>" class="mg-r-10" style="font-size: 16px; width: 24px; height: 24px;"
                alt="<?php echo $item["name"]; ?>">
            <?php else: ?>
              <i class="<?php echo $item["icon"]; ?> mg-r-10" style="font-size: 16px;"></i>
            <?php endif; ?>

            <?php echo $item["name"]; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>