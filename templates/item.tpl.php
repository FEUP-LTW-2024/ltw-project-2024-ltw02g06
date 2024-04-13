<?php
declare(strict_types=1);

require_once (__DIR__ . '/../database/item.class.php');
require_once (__DIR__ . '/../utils/session.php');
?>

<?php function drawItem(?Item $item, Session $session)
{ ?>
  <!-- This is currently static - TODO make dinamic:
    -> Get item id & then get info from db;
    -> If seller ID == session.id: Show and allow user to edit item info; -->
  <article id="item">

    <div id="item-image-container">
      <div>
        <img src="https://ireland.apollo.olxcdn.com/v1/files/5inzf0kibmye2-PT/image;s=1000x700" alt="Item Image 1">
        <img style="display: none;" src="https://ireland.apollo.olxcdn.com/v1/files/hr1b471vsfwx2-PT/image;s=1000x700"
          alt="Item Image 2">
        <img style="display: none;" src="https://ireland.apollo.olxcdn.com/v1/files/f6kwsutrjj071-PT/image;s=1000x700"
          alt="Item Image 3">
        <button id="previous-image-btn"><ion-icon name="chevron-back"></ion-icon></button>
        <button id="next-image-btn"><ion-icon name="chevron-forward"></ion-icon></button>
      </div>
    </div>

    <div id="item-description-container">
      <h2>
        Descrição
      </h2>
      <p>
        Vendo Lexus GS450 Hybrid de Junho de 2007, Nacional.
        <br>
        <br>
        Viatura em excelente estado de conservação.<br>
        <br>
        Versão com bastantes extras dos quais se destacam:<br>
        - Bancos elétricos, aquecidos e arrefecidos com memória<br>
        - Jantes 18” originais<br>
        - Volante em Alcântara com regulação elétrica e memória <br>
        - Suspensão adaptativa<br>
        - Modos de condução<br>
        - Bi Xenon com faróis direcionais<br>
        - Vidros traseiros escurecidos<br>
        - Cortina traseira elétrica<br>
        - Espelhos retrovisores elétricos<br>
        - Keyless<br>
        - Ecrã central totalmente funcional com AC digital e todos os controlos sincronizados com a viatura <br>
        - AC automático bizona <br>
        - Câmara de marcha atrás <br>
        - Cruise Control adaptativo <br>
        - Interior em pele bege<br>
        - Entre muitos outros<br>
        <br>
        Viatura em excelente estado de mecânica. Motor 3.5cc V6 a gasolina com fiabilidade Toyota aliado a um motor
        elétrico o que perfaz uma potência combinada de 345cv e permite mesmo assim consumos baixos para a sua cilindrada
        e potência. Em condução mista é um carro capaz de fazer 8L/100km <br>
        Interior bastante conservado sem sinais de desgaste, entregue devidamente limpo e higienizado.<br>
        Pintura muito bem conservada tal como demonstram as fotos. <br>
        <br>
        Duas chaves e manuais originais com registos de manutenções na marca até aos 140000 km. Foi realizado no passado
        ano check up na marca onde o carro não apresentou qualquer anomalia e comprovou o excelente estado da bateria.<br>
        <br>
        Viatura extremamente confortável, espaçosa, fiável, com performances surpreendentes e com uma enorme presença.
        Look bastante atual, assim como toda a sua tecnologia.<br>
        <br>
        Pode trazer mecânico ou realizar check up na marca.<br>
        <br>
        Sendo de Junho de 2007, ainda está abrangido pelo IUC mais baixo.<br>
        <br>
        Garantia de 18 meses incluída no valor anunciado.<br>
        Possibilidade de financiamento.<br>
        <br>
        Qualquer assunto contactar.
      </p>
      <ul id="item-category-list">
        <li>
          <p>Profissional</p>
        </li>
        <li>
          <p>Segmento: Sedan</p>
        </li>
        <li>
          <p>Modelo: GS 450h</p>
        </li>
        <li>
          <p>Mês de Registo: Junho</p>
        </li>
        <li>
          <p>Ano: 2007</p>
        </li>
        <li>
          <p>Cilindrada: 3.500</p>
        </li>
        <li>
          <p>Combustível: Híbrido</p>
        </li>
        <li>
          <p>Potência: 345</p>
        </li>
        <li>
          <p>Quilómetros: 245.000 km</p>
        </li>
        <li>
          <p>Tipo de Caixa: Automática</p>
        </li>
        <li>
          <p>Condição: Usado</p>
        </li>
        <li>
          <p>Portas: 4-5</p>
        </li>
        <li>
          <p>Lugares: 5</p>
        </li>
        <li>
          <p>Origem: Nacional</p>
        </li>
      </ul>
      <div>
        <p>ID: 653988304</p>
        <p>Cliques: 1057</p>
        <button id="report-button">
          Reportar
        </button>
      </div>
    </div>

    <div id="item-info">
      <div>
        <p class="small-font-size">Publicado 08 de abril de 2024</p>
        <button id="whishlist-btn"><ion-icon name="heart-outline"></ion-icon></button>
      </div>
      <h3 id="item-name">Lexus GS 450H - Garantia - Nacional - Bastantes Extras - 345cv</h3>
      <h2 id="item-price">15.990 €</h2>
      <button id="add-to-cart-btn">Adicionar ao carrinho</button>
      <button id="negotiate-btn">Propor outro preço</button>
      <button id="send-message-btn">Enviar mensagem</button>
    </div>

    <div id="item-location">
      <h3>Localização</h3>
      <div>
        <div>
          <h4>Custóias, Leça Do Balio E Guifões,</h4>
          <p>Porto</p>
        </div>
        <img src="https://www.olx.pt/app/static/media/staticmap.65e20ad98.svg" alt="Location Map">
      </div>
    </div>

    <div id="seller-info">
      <h3>Utilizador</h3>
      <div>
        <img id="seller-img"
          src="https://publish-p47754-e237306.adobeaemcloud.com/adobe/dynamicmedia/deliver/dm-aid--3a108752-74c3-4677-aefc-32338d719b1c/_330186262719.app.png?preferwebp=true&width=312"
          alt="User Profile Picture">
        <div>
          <h4 id="seller-name">Luís Figo</h4>
          <p class="small-font-size">No eKo desde abril de 2015</p>
          <p class="small-font-size">Esteve online dia 07 de abril de 2024</p>
        </div>
      </div>
      <div>
        <h6>Rating 4.3/5</h6>
        <p class="small-font-size">17 classificações</p>
      </div>
      <button id="see-all-items-btn">
        <span>Todos os anúncios deste anunciante</span>
        <span>&gt;</span>
      </button>
    </div>

  </article>
<?php } ?>

<!-- TODO -->
<?php function drawEditItem()
{ ?>

<?php } ?>