/**
 * @description selection range API
 * @author wangfupeng
 */
import { DomElement } from '../utils/dom-core';
import Editor from './index';
declare class SelectionAndRange {
    editor: Editor;
    private _currentRange;
    constructor(editor: Editor);
    /**
     * 获取当前 range
     */
    getRange(): Range | null | undefined;
    /**
     * 保存选区范围
     * @param _range 选区范围
     */
    saveRange(_range?: Range): void;
    /**
     * 折叠选区范围
     * @param toStart true 开始位置，false 结束位置
     */
    collapseRange(toStart?: boolean): void;
    /**
     * 获取选区范围内的文字
     */
    getSelectionText(): string;
    /**
     * 获取选区范围的 DOM 元素
     * @param range 选区范围
     */
    getSelectionContainerElem(range?: Range): DomElement | undefined;
    /**
     * 选区范围开始的 DOM 元素
     * @param range 选区范围
     */
    getSelectionStartElem(range?: Range): DomElement | undefined;
    /**
     * 选区范围结束的 DOM 元素
     * @param range 选区范围
     */
    getSelectionEndElem(range?: Range): DomElement | undefined;
    /**
     * 选区是否为空（没有选择文字）
     */
    isSelectionEmpty(): boolean;
    /**
     * 恢复选区范围
     */
    restoreSelection(): void;
    /**
     * 创建一个空白（即 &#8203 字符）选区
     */
    createEmptyRange(): void;
    /**
     * 根据 DOM 元素设置选区
     * @param $elem DOM 元素
     * @param toStart true 开始位置，false 结束位置
     * @param isContent 是否选中 $elem 的内容
     */
    createRangeByElem($elem: DomElement, toStart?: boolean, isContent?: boolean): void;
    /**
     * 获取 当前 选取范围的 顶级(段落) 元素
     * @param $editor
     */
    getSelectionRangeTopNodes(): DomElement[];
    /**
     * 移动光标位置,默认情况下在尾部
     * 有一个特殊情况是firefox下的文本节点会自动补充一个br元素，会导致自动换行
     * 所以默认情况下在firefox下的文本节点会自动移动到br前面
     * @param {Node} node 元素节点
     * @param {number} position 光标的位置
     */
    moveCursor(node: Node, position?: number): void;
    /**
     * 获取光标在当前选区的位置
     */
    getCursorPos(): number | undefined;
    /**
     * 清除当前选区的Range,notice:不影响已保存的Range
     */
    clearWindowSelectionRange(): void;
}
export default SelectionAndRange;
