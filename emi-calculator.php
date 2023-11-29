<?php
/*
Plugin Name: EMI Calculator
Description: Custom EMI calculator plugin
Version: 1.0
Author: Prashant Deshmukh
*/
function emi_calculator_enqueue_styles() {
    wp_enqueue_style('emi-calculator-styles', plugins_url('emi-calculator.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'emi_calculator_enqueue_styles');

function emi_calculator_shortcode() {
    ?>
    <div id="emi-calculator-container">
        <!-- <h2><?php //echo esc_html__('Loan Details Form', 'emi-calculator'); ?></h2> -->
        <form method="post" id="emi-calculator-form">
            <label for="loanAmount"><?php echo esc_html__('Loan Amount:', 'emi-calculator'); ?></label>
            <input type="number" name="loanAmount" min="0" max="6000000" id="loanAmount" required><br><br>
            
            <label for="interestRate"><?php echo esc_html__('Interest Rate (%):', 'emi-calculator'); ?></label>
            <input type="number" step="0.01" name="interestRate" min="0" max="20" id="interestRate" required><br><br>

            <label for="loanTenure"><?php echo esc_html__('Loan Tenure (months):', 'emi-calculator'); ?></label>
            <input type="number" name="loanTenure" min="0" max="360" id="loanTenure" required><br><br>
            <input type="submit" value="<?php echo esc_html__('Calculate EMI', 'emi-calculator'); ?>">
        </form>
    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $loanAmount = intval($_POST["loanAmount"]);
        $interestRate = floatval($_POST["interestRate"]) / 100;
        $loanTenure = intval($_POST["loanTenure"]);
        if ($loanAmount <= 0 || $interestRate <= 0 || $loanTenure <= 0) {
            echo '<p>' . esc_html__('Please enter valid input values.', 'emi-calculator') . '</p>';
        } else {
            echo "<div id='emi-details'>"; 
            echo "<h2>" . esc_html__('Loan Details', 'emi-calculator') . "</h2>";
            echo "<p>" . esc_html__('Loan Amount', 'emi-calculator') . " : $loanAmount</p>";
            echo "<p>" . esc_html__('Interest Rate', 'emi-calculator') . " : " . ($_POST["interestRate"]) . "%</p>";
            echo "<p>" . esc_html__('Loan Tenure', 'emi-calculator') . " : $loanTenure months</p>";
        
            $emi = calculateEMI($loanAmount, $interestRate, $loanTenure);
            echo "<p>" . esc_html__('Monthly EMI', 'emi-calculator') . " : $emi</p>";
            echo "</div>";
            echo "</div>";
        }
    }
}

function calculateEMI($loanAmount, $interestRate, $loanTenure) {
    $monthlyInterestRate = $interestRate / 12;
    $numMonths = $loanTenure;
    $emi = $loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $numMonths) / (pow(1 + $monthlyInterestRate, $numMonths) - 1);

    return number_format($emi, 2);
}

add_shortcode('emi_calculator', 'emi_calculator_shortcode');
