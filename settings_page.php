<?php namespace rich_text_compress;
/**
 * Tutaj znajduje sie tresc wyswietlana w ustawieniach modulu
 **/
?>

<?php 

// include_once(realpath(__DIR__.'variables.php'));

global $wpdb;

global $db_k_name;
global $db_ks_name;

// print_r($_POST);

if($_POST['edited']==='edited' && $_POST['IdSlownika'] != ''){
    //Zapisywanie wprowadzonych zmian
    // echo 'POST';
    $wpdb->update($db_ks_name,[
        'OpisKursu' => $_POST['ssk-description-editor']
    ],[
        'IdSlownika' => $_POST['IdSlownika']
    ]);
}

$slowniki = $wpdb->get_results("SELECT * FROM `{$db_ks_name}`");
$id_slownika = $_GET['id_slownika'];
echo "<h1>StatSoft Kursy</h1>";
echo '<form method="post" action="#">';
echo '<p><b>Kurs do edycji: </b><select id="ss-select-slownik" name="IdSlownika">';
if($id_slownika == '') echo '<option disabled selected></option>';
// var_dump($id_slownika);
foreach ($slowniki as $rekord) {
    $more = $rekord->IdSlownika === $id_slownika ? 'selected' : '';
    echo "<option value=\"{$rekord->IdSlownika}\" {$more}>{$rekord->NazwaKursu}</option>"; 
}
echo '</select></p>';

$editor_content = '';
if($id_slownika != '' && is_numeric($id_slownika)){
    $row = $wpdb->get_row("SELECT * FROM `{$db_ks_name}` WHERE `IdSlownika` = {$id_slownika}");
    // echo 'GO';
    // var_dump($row);
    $editor_content = $row->OpisKursu;
    wp_editor($editor_content,'ssk-description-editor');
    echo '<input type="hidden" value="edited" name="edited">';
    // echo "<input type=\"hidden\" value=\"{$id_slownika}\" name=\"IdSlownika\">";
    echo '<p><input type="submit" style="float:right;"></p>';
    echo '<div style="clear:both;"></div>';
    echo "<p><b>Uwaga:</b> Fraza <i>[terminy-szkolen]</i> zostanie zastąpiona dniami przeprowadzania kursów.</p>";
}
echo '</form>';
?>
<script type="text/javascript">
    jQuery("#ss-select-slownik").on('change',function(){
        console.log(this)
        let search = location.search.replace(/[&?]id_slownika=[^&#]*/im,'');
        location = location.origin + location.pathname + search + (search == '' ? '?' : '&') + 'id_slownika=' + this.value;
    });
</script>