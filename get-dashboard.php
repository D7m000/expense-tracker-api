<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

require_once 'database.php';

try {
    $user_id = 1; // مؤقتاً
    
    $current_month = date('m');

    $current_year = date('Y');
    
    $sql_total = "SELECT COALESCE(SUM(amount), 0) as total 
                    FROM expenses 
                    WHERE user_id = ? 
                    AND MONTH(expense_date) = ? 
                    AND YEAR(expense_date) = ?";
    
    $stmt_total = $pdo->prepare($sql_total);
    $stmt_total->execute([$user_id, $current_month, $current_year]);
    $total_result = $stmt_total->fetch();
    $current_month_total = floatval($total_result['total']);
    
    $sql_budget = "SELECT monthly_budget FROM users WHERE id = ?";
    $stmt_budget = $pdo->prepare($sql_budget);
    $stmt_budget->execute([$user_id]);
    $budget_result = $stmt_budget->fetch();
    $monthly_budget = floatval($budget_result['monthly_budget'] ?? 0);
    
    $remaining_budget = $monthly_budget - $current_month_total;
    $budget_percentage = $monthly_budget > 0 ? ($current_month_total / $monthly_budget) * 100 : 0;
    
    $sql_by_category = "SELECT 
                            c.name,
                            c.color,
                            c.icon,
                            COALESCE(SUM(e.amount), 0) as total
                        FROM categories c
                        LEFT JOIN expenses e ON c.id = e.category_id 
                            AND e.user_id = ? 
                            AND MONTH(e.expense_date) = ? 
                            AND YEAR(e.expense_date) = ?
                        WHERE c.user_id = ?
                        GROUP BY c.id, c.name, c.color, c.icon
                        HAVING total > 0
                        ORDER BY total DESC";
    
    $stmt_by_category = $pdo->prepare($sql_by_category);
    $stmt_by_category->execute([$user_id, $current_month, $current_year, $user_id]);
    $by_category = $stmt_by_category->fetchAll();
    
    $sql_recent = "SELECT 
                        e.id,
                        e.amount,
                        e.description,
                        e.receipt_image,
                        DATE_FORMAT(e.expense_date, '%Y-%m-%d') as expense_date,
                        c.name as category_name,
                        c.color as category_color,
                        c.icon as category_icon
                    FROM expenses e
                    JOIN categories c ON e.category_id = c.id
                    WHERE e.user_id = ?
                    ORDER BY e.expense_date DESC, e.id DESC
                    LIMIT 5";
    
    $stmt_recent = $pdo->prepare($sql_recent);
    $stmt_recent->execute([$user_id]);
    $recent_expenses = $stmt_recent->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'current_month_total' => $current_month_total,
            'monthly_budget' => $monthly_budget,
            'remaining_budget' => $remaining_budget,
            'budget_percentage' => round($budget_percentage, 2),
            'by_category' => $by_category,
            'recent_expenses' => $recent_expenses
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>