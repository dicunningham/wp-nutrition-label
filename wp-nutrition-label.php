<?php
/*
Plugin Name: Custom Nutrition Facts Labels
Plugin URI: https://github.com/dicunningham/wp-nutrition-label
Description:  Add FDA-style nutrition labels to pages and posts.
Text Domain: custom-nutrition-label
Domain Path: /languages
Author: Danie Cunningham
Version: 1.0


Forked from: http://romkey.com/code/wp-nutrition-label
*/



/* add_action('widgets_init', create_function('', 'return register_widget("NutriLabelWidget");')); */
add_shortcode('nutr-label', 'nutr_label_shortcode');
add_action('wp_head', 'nutr_style');
add_action('init', 'nutr_load_plugin_textdomain');

/*
 * Add local textdomain
 */
function nutr_load_plugin_textdomain() {
  load_plugin_textdomain('wp-nutrition-label', false, 'wp-nutrition-label/languages/');
}

/*
 * output our style sheet at the head of the file
 * because it's brief, we just embed it rather than force an extra http fetch
 *
 * @return void
 */
function nutr_style() {
?>
<style type='text/css'>
.nutrition-facts {
  border: 1px solid black;
  margin: 0 20px 0 10px;
  float: left;
  width: 280px;
  padding: 0.5rem;
  box-sizing:content-box;
}
.nutrition-facts tbody tr:nth-child(2n) {
  background: white;
}
.nutrition-facts thead tr{
  background: white;
  color: black;
}
.nutrition-facts p,
.nutrition-facts b, 
.nutrition-facts th, 
.nutrition-facts td, 
.nutrition-facts strong {
  font-family: Helvetica, Arial, sans-serif;
  line-height: 1.4;
  font-size: small;
  color: black;
  margin: 0;
  word-break: keep-all;
}
.nutrition-facts h1 {
  font-family: Helvetica, Arial, sans-serif;
  line-height: 1.4;
  color: black;
  margin: 0;
}
.nutrition-facts table {
  border-collapse: collapse;
}

.nutrition-facts__title {
  font-weight: bold;
  font-size: 2rem;
  margin: 0 0 0.25rem 0;
}

.nutrition-facts__header {
  border-bottom: 10px solid black;
  padding: 0 0 0.25rem 0;
  margin: 0 0 0.5rem 0;
}
.nutrition-facts__header p {
  margin: 0;
}

.nutrition-facts__table,  .nutrition-facts__table--grid {
  width: 100%;
}
.nutrition-facts__table thead tr th, 
.nutrition-facts__table--grid thead tr th, 
.nutrition-facts__table thead tr td, 
.nutrition-facts__table--grid thead tr td {
  border: 0;
}
.nutrition-facts__table th, .nutrition-facts__table--grid th, 
.nutrition-facts__table td, .nutrition-facts__table--grid td {
  font-weight: normal;
  text-align: left;
  padding: 0.2rem 0;
  border-top: 1px solid black;
  white-space: nowrap;
}
.nutrition-facts__table td:last-child {
  text-align:right;
}
.nutrition-facts__table--grid td:nth-child(2n) {
  text-align: center;
}

.nutrition-facts__table .thick-row th, 
.nutrition-facts__table--small .thick-row th, 
.nutrition-facts__table--grid .thick-row th, 
.nutrition-facts__table .thick-row td, 
.nutrition-facts__table--small .thick-row td, 
.nutrition-facts__table--grid .thick-row td {
  border-top-width: 5px;
}

.nutrition-facts .small-info {
  font-size: 0.7rem;
}
.page-id-9958 .warn {
  font-size: 1.25em;
  text-transform: uppercase;
  font-weight: bold;
}

.nutrition-facts__table--grid {
  margin: 0 0 0.5rem 0;
}
.nutrition-facts__table--grid td:last-child {
  text-align: right;
}

.text-center {
  text-align: center;
}

.thick-end {
  border-bottom: 10px solid black;
}

.thin-end {
  border-bottom: 1px solid black;
}
</style>
<?php
    }

/* attributes we look for:
 *    servingsize, servings, calories, totalfat, satfat, transfat, cholestrol, sodium, carbohydrates, fiber, sugars, protein
 * also,
 *    id, class
 *
 * @param array $atts
 * @return string
 */
function nutr_label_shortcode($atts) {
  $args = shortcode_atts( array(servingsize => 0,
				 servings => 0,
				 calories => 0,
         caloriesfat => 0,
				 totalfat => 0,
         totalfat_rda => 0,
				 satfat => 0,
         satfat_rda => 0,
				 transfat => 0,
				 cholesterol => 0,
         cholesterol_rda => 0,
				 sodium => 0,
         sodium_rda => 0,
				 carbohydrates => 0,
         carbohydrates_rda => 0,
				 fiber => 0,
         fiber_rda => 0,
				 sugars => 0,
				 protein => 0,
			 	 vitamin_a => 0,
				 viamin_c => 0,
         calcium => 0,
         iron => 0,
				 width => 22,
				 id => '',
				 cssclass => '' ), $atts );
  return nutr_label_generate($args);
}


/*
 * @param array $args
 * @return string
 */
function nutr_label_generate($args) {
  extract($args, EXTR_PREFIX_ALL, 'nutr');
  if($nutr_calories == 0) {
    $nutr_calories = (($nutr_protein + $nutr_carbohydrates)*4) + ($nutr_totalfat * 9);
  }


  /* attempt to restyle the label */
  $style = '';
  if($nutr_width != 22) {
    $style = "style='width: ".$nutr_width."em; font-size: ".(($nutr_width/22)*.75)."em;'";
  }

  return "<section ".($nutri_id ? "id='".$nutri_id."'" : "") . ($style ? $style : "") . "class='nutrition-facts" . ( $nutri_cssclass ? " ".$nutri_cssclass : "") . "'>

  <header class='nutrition-facts__header'>
    <h1 class='nutrition-facts__title'>".__("Nutrition Facts")."</h1>
    <p>".__("Serving Size")." ".$nutr_servingsize."</p>
    <p>".__("Servings")." ".$nutr_servings."</p>
  </header>

  <table class='nutrition-facts__table'>
    <thead>
      <tr>
        <th class='small-info' colspan='3'>Amount Per Serving</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <th colspan='2'><strong>".__("Calories")."</strong> ".$nutr_calories."</th>
        <td>Calories from Fat ".$nutr_caloriesfat."</td>
      </tr>
  
      <tr class='thick-row'>
        <td class='small-info' colspan='3'><strong>% ".__("Daily Value")."*</strong></td>
      </tr>
      <tr>
        <th colspan='2'><strong>".__("Total Fat")."</strong> ".$nutr_totalfat."g</th>
        <td>".$nutr_totalfat_rda."%</td>
      </tr>
      <tr>
        <td class='blank-cell'></td>
        <th>".__("Saturated Fat")." ".$nutr_satfat."g</th>
        <td><strong>".$nutr_satfat_rda."%</strong></td>
      <tr>
        <td class='blank-cell'></td>
          <th>".__("Trans Fat")." ".$nutr_transfat."g</th>
          <td></td>
        </tr>
        <tr>
          <th colspan='2'><strong>".__("Cholesterol")."</strong> ".$nutr_cholesterol."mg</th>
          <td><strong>".$nutr_cholesterol_rda."%</strong></td>
          </tr>
          <tr>
            <th colspan='2'><strong>".__("Sodium")."</strong> ".$nutr_sodium."mg</th>
            <td><strong>".$nutr_sodium_rda."%</strong></td>
          </tr>
          <tr>
            <th colspan='2'><strong>".__("Total Carbohydrate")."</strong> ".$nutr_carbohydrates."g</th>
            <td><strong>".$nutr_carbohydrates_rda."%</strong></td>
          </tr>
          <tr>
            <td class='blank-cell'></td>
            <th>".__("Dietary Fiber")." ".$nutr_fiber."g</th>
            <td><strong>".$nutr_fiber_rda."%</strong></td>
          </tr>
          <tr>
            <td class='blank-cell'></td>
            <th>".__("Sugars")." ".$nutr_sugars."g</th>
            <td></td>
            </tr>
          <tr class='thick-end'>
            <th colspan='2'><strong>".__("Protein")."</strong> ".$nutr_protein."g</th>
            <td></td>
          </tr>
        </tbody>
      </table>
      <table class='nutrition-facts__table--grid'>
        <tbody>
          <tr>
            <td colspan='2'>".__("Vitamin A")." ".$nutr_vitamin_a."%</td>
            <td>●</td>
            <td>".__("Vitamin C")." ".$nutr_vitamin_c."%</td>
          </tr>
          <tr class='thin-end'>
            <td colspan='2'>".__("Calcium")." ".$nutr_calcium."%</td>
            <td>●</td>
            <td>".__("Iron")." ".$nutr_iron."%</td>
          </tr>
        </tbody>
      </table>
      <p class='small-info'>* ".__("Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.")."</p>
    </section>";
} ?>
