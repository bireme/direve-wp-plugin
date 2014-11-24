<?php
/*
Template Name: DirEVE Suggest Event
*/
global $direve_service_url, $direve_plugin_slug;

$direve_config = get_option('direve_config');

$current_year = intval(date("Y"));

$year_list = range($current_year, $current_year+4);
$day_list = range(1,31);
$month_list = range(1,12);

?>

<?php get_header('direve'); ?>

<script language="javascript">

    function getSelectVal(element_id){
        var el = document.getElementById(element_id);
        var el_value = el.options[el.selectedIndex].value;

        return el_value;
    }

    function joinDate(){
        
        document.getElementById('start_date').value = getSelectVal('start_day') + '/' + getSelectVal('start_month') + '/'  + getSelectVal('start_year');
        document.getElementById('end_date').value = getSelectVal('end_day') + '/' + getSelectVal('end_month') + '/' + getSelectVal('end_year');

    }
</script>


<div id="content" class="row-fluid">
        <div class="ajusta2">
            <div class="row-fluid">
                <a href="<?php echo real_site_url(); ?>"><?php _e('Home','direve'); ?></a> > 
                <a href="<?php echo real_site_url($direve_plugin_slug); ?>"><?php _e('Events Directory', 'direve') ?> </a> > 
                <?php _e('Suggest a event','direve'); ?>
            </div>

            <section id="conteudo">
                <header class="row-fluid border-bottom">
                    <h1 class="h1-header"><?php _e('Suggest a event','direve'); ?></h1>
                </header>
                <div class="row-fluid">
                    <article class="conteudo-loop suggest-form">

                        <form method="post" name="suggest_form" action="<?php echo $direve_service_url ?>suggest-event" onsubmit="joinDate()">
                            <input type="hidden" name="start_date" id="start_date" value="" />
                            <input type="hidden" name="end_date" id="end_date" value="" />

                            <?php _e('Event title', 'direve') ?>
                            <p><input type="text"  name="title" size="80" value=""/></p>

 
                            <div class="row-fluid">
                                <div><?php _e('Start date', 'direve') ?></div>

                                <select name="start_day" id="start_day">
                                    <option value="" selected="selected"><?php _e('Day', 'direve') ?></option>
                                    <?php foreach ($day_list as $day) :?>
                                        <option value="<?php echo $day ?>"><?php echo $day ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="start_month" id="start_month">
                                    <option value="" selected="selected"><?php _e('Month', 'direve') ?></option>
                                    <?php foreach ($month_list as $month) :?>
                                        <option value="<?php echo $month ?>"><?php echo $month ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="start_year" id="start_year">
                                    <option value="" selected="selected"><?php _e('Year', 'direve') ?></option>
                                    <?php foreach ($year_list as $year) :?>
                                        <option value="<?php echo $year ?>"><?php echo $year ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row-fluid">
                                <div><?php _e('End date', 'direve') ?></div>
                                <select name="end_day" id="end_day">
                                    <option value="" selected="selected"><?php _e('Day', 'direve') ?></option>
                                    <?php foreach ($day_list as $day) :?>
                                        <option value="<?php echo $day ?>"><?php echo $day ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="end_month" id="end_month">
                                    <option value="" selected="selected"><?php _e('Month', 'direve') ?></option>
                                    <?php foreach ($month_list as $month) :?>
                                        <option value="<?php echo $month ?>"><?php echo $month ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="end_year" id="end_year">
                                    <option value="" selected="selected"><?php _e('Year', 'direve') ?></option>
                                    <?php foreach ($year_list as $year) :?>
                                        <option value="<?php echo $year ?>"><?php echo $year ?></option>
                                    <?php endforeach; ?>
                                </select> 
                            </div>

                            <?php _e('Website', 'direve') ?>
                            <p><input type="text" placeholder="" name="link" size="80" value="<?php echo $site_meta_tags['keywords'] ?>"/></p>

                            <?php _e('City', 'direve') ?>
                            <p><input type="text" placeholder="" name="city" size="80" value="<?php echo $site_meta_tags['keywords'] ?>"/></p>
                            
                            <div><?php _e('Country', 'direve') ?></div>
                            <select id="country" name="country">
                                <option value="1">Afeganistão</option>
                                <option value="2">Albânia</option>
                                <option value="3">Argélia</option>
                                <option value="4">Samoa americana</option>
                                <option value="5">Andorra</option>
                                <option value="6">Angola</option>
                                <option value="7">Anguila</option>
                                <option value="8">Antártida</option>
                                <option value="9">Antigua e Barbuda</option>
                                <option value="10">Argentina</option>
                                <option value="11">Armênia</option>
                                <option value="12">Aruba</option>
                                <option value="13">Australia</option>
                                <option value="14">Austria</option>
                                <option value="15">Azerbaidjão</option>
                                <option value="16">Bahamas</option>
                                <option value="17">Bahrein</option>
                                <option value="18">Bangladesh</option>
                                <option value="19">Barbados</option>
                                <option value="20">Belarus</option>
                                <option value="21">Bélgica</option>
                                <option value="22">Belize</option>
                                <option value="23">Benim</option>
                                <option value="24">Bermudas</option>
                                <option value="25">Butão</option>
                                <option value="26">Bolívia</option>
                                <option value="27">Bosnia-Herzegovina</option>
                                <option value="28">Botsuana</option>
                                <option value="29">Bouvet, Ilha</option>
                                <option value="30" selected="selected">Brasil</option>
                                <option value="31">Territorio britânico no oceano índico</option>
                                <option value="32">Brunei Darussalam</option>
                                <option value="33">Búlgaria</option>
                                <option value="34">Burquina Fasso</option>
                                <option value="35">Burundi</option>
                                <option value="36">Camboja</option>
                                <option value="37">Camarões</option>
                                <option value="38">Canadá</option>
                                <option value="39">Cabo Verde</option>
                                <option value="40">Cayman, Ilhas</option>
                                <option value="41">Centro-Africana, República</option>
                                <option value="42">Chade</option>
                                <option value="43">Chile</option>
                                <option value="44">China</option>
                                <option value="45">Christmas, Ilhas</option>
                                <option value="46">Cocos, Ilhas</option>
                                <option value="47">Colômbia</option>
                                <option value="48">Comores</option>
                                <option value="49">Congo</option>
                                <option value="50">Cook, Ilhas</option>
                                <option value="51">Costa Rica</option>
                                <option value="52">Costa do Marfim</option>
                                <option value="53">Croácia</option>
                                <option value="54">Cuba</option>
                                <option value="55">Chipre</option>
                                <option value="56">Tcheca, República</option>
                                <option value="57">Dinamarca</option>
                                <option value="58">Djibuti</option>
                                <option value="59">Dominica</option>
                                <option value="60">Dominicana, República</option>
                                <option value="61">Timor Oriental</option>
                                <option value="62">Equador</option>
                                <option value="63">Egito</option>
                                <option value="64">El Salvador</option>
                                <option value="65">Guine Equatorial</option>
                                <option value="66">Eritréia</option>
                                <option value="67">Estônia</option>
                                <option value="68">Etiópia</option>
                                <option value="69">Falkland, Ilhas</option>
                                <option value="70">Faroe, Ilhas</option>
                                <option value="71">Fiji</option>
                                <option value="72">Finlândia</option>
                                <option value="73">França</option>
                                <option value="74">França (territorio europeu)</option>
                                <option value="75">Guiana Francesa</option>
                                <option value="76">Polinesia Francesa</option>
                                <option value="77">Territorios Franceses Meridionais</option>
                                <option value="78">Gabão</option>
                                <option value="79">Gâmbia</option>
                                <option value="80">Georgia</option>
                                <option value="81">Alemanha</option>
                                <option value="82">Gana</option>
                                <option value="83">Gibraltar</option>
                                <option value="84">Grécia</option>
                                <option value="85">Groenlândia</option>
                                <option value="86">Granada</option>
                                <option value="87">Guadalupe</option>
                                <option value="88">Guam</option>
                                <option value="89">Guatemala</option>
                                <option value="90">Guine</option>
                                <option value="91">Guine-Bissau</option>
                                <option value="92">Guiana</option>
                                <option value="93">Haiti</option>
                                <option value="94">Heard e McDonald, Ilhas</option>
                                <option value="95">Cidade-Estado do Vaticano</option>
                                <option value="96">Honduras</option>
                                <option value="97">Hong Kong</option>
                                <option value="98">Hungria</option>
                                <option value="99">Islândia</option>
                                <option value="100">Índia</option>
                                <option value="101">Indonésia</option>
                                <option value="102">Irã, República Islâmica do</option>
                                <option value="103">Iraque</option>
                                <option value="104">Irlanda</option>
                                <option value="105">Israel</option>
                                <option value="106">Itália</option>
                                <option value="107">Jamaica</option>
                                <option value="108">Japão</option>
                                <option value="109">Jordânia</option>
                                <option value="110">Cazaquistão</option>
                                <option value="111">Quênia</option>
                                <option value="112">Kiribati</option>
                                <option value="113">Coreia do Norte</option>
                                <option value="114">Coreia do Sul</option>
                                <option value="115">Kuwait</option>
                                <option value="116">Quirguizistão</option>
                                <option value="117">Laos, República Democratica do Povo</option>
                                <option value="118">Letônia</option>
                                <option value="119">Líbano</option>
                                <option value="120">Lessoto</option>
                                <option value="121">Libéria</option>
                                <option value="122">Líbia</option>
                                <option value="123">Liechtenstein</option>
                                <option value="124">Lituânia</option>
                                <option value="125">Luxemburgo</option>
                                <option value="126">Macau</option>
                                <option value="127">Macedônia</option>
                                <option value="128">Madagascar</option>
                                <option value="129">Malawi</option>
                                <option value="130">Malásia</option>
                                <option value="131">Maldivas</option>
                                <option value="132">Mali</option>
                                <option value="133">Malta</option>
                                <option value="134">Marshall, Ilhas</option>
                                <option value="135">Martinica</option>
                                <option value="136">Mauritânia</option>
                                <option value="137">Maurício, Ilhas</option>
                                <option value="138">Mayotte</option>
                                <option value="139">México</option>
                                <option value="140">Micronésia</option>
                                <option value="141">Moldávia</option>
                                <option value="142">Mônaco</option>
                                <option value="143">Mongólia</option>
                                <option value="144">Montserrat</option>
                                <option value="145">Marrocos</option>
                                <option value="146">Moçambique</option>
                                <option value="147">Myanmar</option>
                                <option value="148">Namíbia</option>
                                <option value="149">Nauru</option>
                                <option value="150">Nepal</option>
                                <option value="151">Países Baixos</option>
                                <option value="152">Antilhas Holandesas</option>
                                <option value="153">Nova Caledônia</option>
                                <option value="154">Nova Zelândia</option>
                                <option value="155">Nicarágua</option>
                                <option value="156">Niger</option>
                                <option value="157">Nigéria</option>
                                <option value="158">Niue</option>
                                <option value="159">Norfolk, Ilha</option>
                                <option value="160">Mariana do Norte, Ilhas</option>
                                <option value="161">Noruega</option>
                                <option value="162">Omã</option>
                                <option value="163">Paquistão</option>
                                <option value="164">Palau</option>
                                <option value="165">Panamá</option>
                                <option value="166">Papua-Nova Guiné</option>
                                <option value="167">Paraguai</option>
                                <option value="168">Perú</option>
                                <option value="169">Filipinas</option>
                                <option value="170">Pitcairn, Ilha</option>
                                <option value="171">Polônia</option>
                                <option value="172">Portugal</option>
                                <option value="173">Porto Rico</option>
                                <option value="174">Qatar</option>
                                <option value="175">Reunião</option>
                                <option value="176">Romênia</option>
                                <option value="177">Federacao Russa</option>
                                <option value="178">Ruanda</option>
                                <option value="179">Saint Kitts Nevis Anguilla</option>
                                <option value="180">Santa Lúcia</option>
                                <option value="181">São Vicente e Granada</option>
                                <option value="182">Samôa</option>
                                <option value="183">San Marino</option>
                                <option value="184">São Tomé e Principe</option>
                                <option value="185">Arábia Saudita</option>
                                <option value="186">Senegal</option>
                                <option value="187">Seychelles</option>
                                <option value="188">Serra Leoa</option>
                                <option value="189">Singapura</option>
                                <option value="190">Eslovaca, República</option>
                                <option value="191">Eslovênia</option>
                                <option value="192">Salomão, Ilhas</option>
                                <option value="193">Somália</option>
                                <option value="194">África do Sul</option>
                                <option value="195">Georgia do Sul e Ilhas Sandwich do Sul</option>
                                <option value="196">Espanha</option>
                                <option value="197">Sri Lanka</option>
                                <option value="198">Santa Helena</option>
                                <option value="199">Saint Pierre e Miquelon</option>
                                <option value="200">Sudão</option>
                                <option value="201">Suriname</option>
                                <option value="202">Svalbard e Jan Mayen</option>
                                <option value="203">Suazilândia</option>
                                <option value="204">Suécia</option>
                                <option value="205">Suiça</option>
                                <option value="206">Síria</option>
                                <option value="207">Taiwan</option>
                                <option value="208">Tadjiquistão</option>
                                <option value="209">Tanzânia</option>
                                <option value="210">Tailândia</option>
                                <option value="211">Togo</option>
                                <option value="212">Tokelau</option>
                                <option value="213">Tonga</option>
                                <option value="214">Trinidad e Tobago</option>
                                <option value="215">Tunísia</option>
                                <option value="216">Turquia</option>
                                <option value="217">Turcomenistão</option>
                                <option value="218">Turcas e Caicos, Ilhas</option>
                                <option value="219">Tuvalu</option>
                                <option value="220">Uganda</option>
                                <option value="221">Ucrânia</option>
                                <option value="222">Emirados Árabes Unidos</option>
                                <option value="223">Reino Unido</option>
                                <option value="224">Estados Unidos da América</option>
                                <option value="225">Ilhas exteriores menores dos Estados Unidos da América</option>
                                <option value="226">Uruguai</option>
                                <option value="227">Usbequistão</option>
                                <option value="228">Vanuatu</option>
                                <option value="229">Venezuela</option>
                                <option value="230">Vietnã</option>
                                <option value="231">Virgens Britânicas, Ilhas</option>
                                <option value="232">Ilhas Virgens</option>
                                <option value="233">Wallis e Futuna, Ilhas</option>
                                <option value="234">Saara Ocidental</option>
                                <option value="235">Iemen</option>
                                <option value="236">Iugoslavia</option>
                                <option value="237">Zaire</option>
                                <option value="238">Zâmbia</option>
                                <option value="239">Zimbabue</option>
                            </select>


                            <script type="text/javascript">
                                var RecaptchaOptions = {
                                    theme : 'clean',
                                    lang : '<?php echo substr($site_language, 0,2); ?>'
                                };
                            </script>
                            <script type="text/javascript"
                               src="http://www.google.com/recaptcha/api/challenge?k=6LcV0ugSAAAAAEpxBvqmNlnOZIAKSf_E6M-s8abc">
                            </script>
                            <noscript>
                               <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcV0ugSAAAAAEpxBvqmNlnOZIAKSf_E6M-s8abc"
                                   height="300" width="500" frameborder="0"></iframe><br>
                               <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                               </textarea>
                               <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                            </noscript>


                            <div class="btn-line">
                               <input type="submit" value="<?php _e('Send', 'direve') ?>"/>
                            </div>

                        </form>
    
                    </article>
                </div>
            </section>

        </div>
    </div>

<?php get_footer();?>
