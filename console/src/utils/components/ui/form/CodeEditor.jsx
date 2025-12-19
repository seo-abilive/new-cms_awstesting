import { useEffect, useState } from 'react'
import CodeMirror from '@uiw/react-codemirror'
import { json } from '@codemirror/lang-json'
import { javascript } from '@codemirror/lang-javascript'
import { html } from '@codemirror/lang-html'
import { css } from '@codemirror/lang-css'
import { oneDark } from '@codemirror/theme-one-dark'
import { EditorView } from '@codemirror/view'
import { twMerge } from 'tailwind-merge'
import { useTheme } from '@/utils/context/ThemeContext'

/**
 * CodeEditor component for editing JSON, JavaScript, HTML, CSS, and other code.
 *
 * @param {object} props - Props passed to CodeEditor.
 * @param {string} props.id - ID of the editor element.
 * @param {string} [props.name] - Name of the editor field.
 * @param {string} [props.value] - Current value of the editor.
 * @param {string} [props.defaultValue] - Default value of the editor.
 * @param {function(string, React.ChangeEvent): void} [props.onChange] - Callback with value and event.
 * @param {string} [props.language='json'] - Language mode (json, javascript, html, css).
 * @param {number} [props.rows=10] - Number of rows (affects height).
 * @param {boolean} [props.darkMode] - Override dark mode (auto-detected if not provided).
 * @param {string} [props.className] - Additional CSS classes.
 * @param {boolean} [props.readOnly=false] - Whether the editor is read-only.
 * @returns {JSX.Element}
 */
export const CodeEditor = ({
    id,
    name,
    value,
    defaultValue = '',
    onChange = () => {},
    language = 'json',
    rows = 10,
    darkMode,
    className,
    readOnly = false,
    ...props
}) => {
    const { theme } = useTheme()
    const [editorValue, setEditorValue] = useState(value ?? defaultValue ?? '')
    
    // ダークモードを自動検出（propsで指定されていない場合）
    const isDarkMode = darkMode !== undefined ? darkMode : theme === 'dark'

    useEffect(() => {
        if (value !== undefined && value !== editorValue) {
            setEditorValue(value)
        }
    }, [value])

    const getLanguageExtension = () => {
        switch (language?.toLowerCase()) {
            case 'javascript':
            case 'js':
                return javascript()
            case 'html':
                return html()
            case 'css':
                return css()
            case 'json':
            default:
                return json()
        }
    }

    const handleChange = (val) => {
        setEditorValue(val)
        onChange(val, { target: { name, value: val } })
    }

    const minHeight = `${rows * 1.5}rem`

    // フォントサイズを大きくするための拡張
    const fontSizeExtension = EditorView.theme({
        '&': {
            fontSize: '15px',
        },
        '.cm-content': {
            fontSize: '15px',
            lineHeight: '1.6',
        },
        '.cm-lineNumbers': {
            fontSize: '15px',
        },
        '.cm-gutters': {
            fontSize: '15px',
        },
    })

    return (
        <div className={twMerge('border rounded-lg overflow-hidden dark:border-gray-600', className)}>
            <CodeMirror
                value={editorValue}
                height={minHeight}
                extensions={[
                    getLanguageExtension(),
                    fontSizeExtension,
                ]}
                theme={isDarkMode ? oneDark : undefined}
                onChange={handleChange}
                readOnly={readOnly}
                basicSetup={{
                    lineNumbers: true,
                    foldGutter: true,
                    dropCursor: false,
                    allowMultipleSelections: false,
                    indentOnInput: true,
                    bracketMatching: true,
                    closeBrackets: true,
                    autocompletion: true,
                    highlightSelectionMatches: false,
                }}
                {...props}
            />
        </div>
    )
}
