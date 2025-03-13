<?php
namespace app\controller;

use app\BaseController;

use Xpyun\model\PrintRequest;
use Xpyun\service\PrintService;
use Xpyun\util\NoteFormatter;

class Index extends BaseController
{
    
    public function index()
    {
        
        $this->service = new PrintService();
        
        //第一个标签
        $printContent = "<PAGE>"
            . "<SIZE>40,30</SIZE>" // 设定标签纸尺寸
            . "<TEXT x=\"8\" y=\"8\" w=\"1\" h=\"1\" r=\"0\">"
            . "#001" . str_repeat(" ", 4)
            . "一号桌" . str_repeat(" ", 4)
            . "1/3"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"96\" w=\"2\" h=\"2\" r=\"0\">"
            . "黄金炒饭"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"200\" w=\"1\" h=\"1\" r=\"0\">"
            . "王女士" . str_repeat(" ", 4)
            . "136****3388"
            . "</TEXT>"
            . "</PAGE>";

        //第二个标签
        $printContent = $printContent . "<PAGE>"
            . "<TEXT x=\"8\" y=\"8\" w=\"1\" h=\"1\" r=\"0\">"
            . "#001" . str_repeat(" ", 4)
            . "一号桌" . str_repeat(" ", 4)
            . "2/3"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"96\" w=\"2\" h=\"2\" r=\"0\">"
            . "凉拌青瓜"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"200\" w=\"1\" h=\"1\" r=\"0\">"
            . "王女士" . str_repeat(" ", 4)
            . "136****3388"
            . "</TEXT>"
            . "</PAGE>";

        //第三个标签
        $printContent = $printContent . "<PAGE>"
            . "<TEXT x=\"8\" y=\"8\" w=\"1\" h=\"1\" r=\"0\">"
            . "#001" . str_repeat(" ", 4)
            . "一号桌" . str_repeat(" ", 4)
            . "3/3"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"96\" w=\"2\" h=\"2\" r=\"0\">"
            . "老刘家肉夹馍"
            . "</TEXT>"
            . "<TEXT x=\"8\" y=\"200\" w=\"1\" h=\"1\" r=\"0\">"
            . "王女士" . str_repeat(" ", 4)
            . "136****3388"
            . "</TEXT>"
            . "</PAGE>";

        //第四个标签 打印条形码
        $printContent = $printContent . "<PAGE>"
            . "<TEXT x=\"8\" y=\"8\" w=\"1\" h=\"1\" r=\"0\">"
            . "打印条形码："
            . "</TEXT>"
            . "<BC128 x=\"16\" y=\"32\" h=\"32\" s=\"1\" n=\"2\" w=\"2\" r=\"0\">"
            . "12345678"
            . "</BC128>"
            . "</PAGE>";

        //第四个标签 打印二维码，宽度最小为128 低于128会无法扫描
        $printContent = $printContent . "<PAGE>"
            . "<TEXT x=\"8\" y=\"8\" w=\"1\" h=\"1\" r=\"0\">"
            . "打印二维码宽度128："
            . "</TEXT>"
            . "<QR x=\"16\" y=\"32\" w=\"128\">"
            . "https://www.xpyun.net"
            . "</QR>"
            . "</PAGE>";

        $request = new PrintRequest('user e-mail', 'user key');
        $request->generateSign();

        //*必填*：打印机编号
        $request->sn = 'print sn';

        //*必填*：打印内容,不能超过12K
        $request->content = $printContent;

        //打印份数，默认为1
        $request->copies = 1;

        //声音播放模式，0 为取消订单模式，1 为静音模式，2 为来单播放模式，3为有用户申请退单了。默认为 2 来单播放模式
        $request->voice = 2;

        //打印模式：
        //值为 0 或不指定则会检查打印机是否在线，如果不在线 则不生成打印订单，直接返回设备不在线状态码；如果在线则生成打印订单，并返回打印订单号。
        //值为 1不检查打印机是否在线，直接生成打印订单，并返回打印订单号。如果打印机不在线，订单将缓存在打印队列中，打印机正常在线时会自动打印。
        $request->mode = 1;

        $result = $this->service->xpYunPrintLabel($request);
        
        // 添加错误处理和日志记录
        if (!isset($result->content)) {
            return json(['code' => -1, 'msg' => '打印服务调用失败']);
        }
        
        echo "返回代码: " . $result->content->code . "\n";
        echo "返回信息: " . $result->content->msg . "\n";
        
        // 只有在成功时才尝试访问 data
        if ($result->content->code == 0) {
            echo "订单编号: " . $result->content->data . "\n";
            return json(['code' => 0, 'msg' => '打印成功', 'data' => $result->content->data]);
        } else {
            return json(['code' => $result->content->code, 'msg' => $result->content->msg]);
        }
    }
}