<!DOCTYPE html>
<html>
<head>
    <title>服务器性能检测面板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f7f7f7;
            padding-top: 50px;
        }
        
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 50px;
        }
        
        h1, h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .btn-group {
            margin-bottom: 10px;
            text-align: center;
        }
        
        .btn-group .btn {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">轻逸面板-Beta 0.0.1</h1>
        
        <h2>系统信息</h2>
        <?php
        $os = PHP_OS;
        $uptime = shell_exec('uptime');
        $cpu = shell_exec('cat /proc/cpuinfo | grep "model name" | head -n 1 | cut -d ":" -f 2');
        $memory = shell_exec('free -h | awk \'/Mem/ {print $2}\'');
        $load_avg = sys_getloadavg();
        
        echo "<p><strong>操作系统：</strong> $os</p>";
        echo "<p><strong>运行时间：</strong> $uptime</p>";
        echo "<p><strong>CPU型号：</strong> $cpu</p>";
        echo "<p><strong>内存大小：</strong> $memory</p>";
        echo "<p><strong>负载平均值：</strong> {$load_avg[0]}, {$load_avg[1]}, {$load_avg[2]}</p>";
        ?>
        
        <h2>CPU占用</h2>
        <?php
        $cpu_usage = shell_exec("top -bn1 | grep \"Cpu(s)\" | awk '{print $2 + $4}'");
        
        echo "<p><strong>CPU占用：</strong> $cpu_usage%</p>";
        ?>
        
        <h2>内存占用</h2>
        <?php
        $memory_usage = shell_exec("free -m | awk '/Mem/ { printf \"%.2f%%\", $3/$2 * 100.0 }'");
        
        echo "<p><strong>内存占用：</strong> $memory_usage</p>";
        ?>
        
        <h2>磁盘空间</h2>
        <?php
        $disk_total = disk_total_space('/');
        $disk_free = disk_free_space('/');
        $disk_used = $disk_total - $disk_free;
        $disk_total_gb = formatBytes($disk_total);
        $disk_used_gb = formatBytes($disk_used);
        $disk_free_gb = formatBytes($disk_free);
        
        echo "<p><strong>总空间：</strong> $disk_total_gb</p>";
        echo "<p><strong>已用空间：</strong> $disk_used_gb</p>";
        echo "<p><strong>可用空间：</strong> $disk_free_gb</p>";
        ?>
        
        <h2>网络信息</h2>
        <?php
        $ip = shell_exec('curl ip.sb');
        $hostname = gethostname();
        
        echo "<p><strong>IP地址：</strong> $ip</p>";
        echo "<p><strong>主机名：</strong> $hostname</p>";
        ?>
        
        <h2>常用命令</h2>
        <div class="btn-group d-flex justify-content-center">
            <button class="btn btn-primary" onclick="executeCommand('bash <(curl -Ls https://raw.githubusercontent.com/vaxilu/x-ui/master/install.sh) -n')">安装X-UI</button>
            <button class="btn btn-primary" onclick="executeCommand('df -h')">磁盘空间</button>
            <button class="btn btn-primary" onclick="executeCommand('sudo -i')">切换ROOT</button>
        </div>
        
        <pre id="commandOutput"></pre>
    </div>
    
    <script>
        function executeCommand(command) {
            fetch('execute.php?command=' + encodeURIComponent(command))
                .then(response => response.text())
                .then(output => {
                    document.getElementById('commandOutput').textContent = output;
                });
        }
    </script>
</body>
</html>

<?php
function formatBytes($bytes, $decimals = 2) {
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}
?>
