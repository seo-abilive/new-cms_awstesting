import React, { useEffect, useRef, useState } from 'react'
import { twMerge } from 'tailwind-merge'
import { EditorContent, useEditor } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import TextAlign from '@tiptap/extension-text-align'
import { Button } from '@/utils/components/ui/button'
import {
    HiOutlineViewList,
    HiOutlineHashtag,
    HiOutlineChatAlt2,
    HiOutlineCode,
    HiOutlineLink,
    HiOutlineX,
    HiOutlineArrowNarrowLeft,
    HiOutlineArrowNarrowRight,
    HiOutlineLightBulb,
} from 'react-icons/hi'
import { CiTextAlignCenter, CiTextAlignLeft, CiTextAlignRight } from 'react-icons/ci'
import { ProofReading } from '../ai/ProofReading'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

export const ReactEditor = ({
    defaultValue = '',
    onChange = () => {},
    readOnly = false,
    placeholder = '入力を開始してください...',
    className,
    editorClassName,
    minHeight = 120,
    paddingXClass = 'px-0',
    isNotAiUse = false,
}) => {
    const proofreadingRef = useRef(null)
    const { isAiUse } = useCompanyFacility()

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: { levels: [1, 2, 3] },
            }),
            Underline,
            Link.configure({ openOnClick: true }),
            TextAlign.configure({
                types: ['heading', 'paragraph'],
            }),
        ],
        content: defaultValue || '',
        editable: !readOnly,
        onUpdate: ({ editor }) => {
            onChange(editor.getHTML())
        },
        editorProps: {
            attributes: {
                class: 'prose prose-sm max-w-none min-h-[50px] px-2 outline-none focus:outline-none focus-visible:outline-none ring-0 focus:ring-0 focus-visible:ring-0 border-0',
            },
        },
    })

    useEffect(() => {
        if (!editor) return
        if (defaultValue && editor.getHTML() !== defaultValue) {
            editor.commands.setContent(defaultValue, false)
        }
    }, [defaultValue, editor])

    return (
        <div
            className={twMerge(
                `border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-white py-2 ${paddingXClass}`,
                className
            )}
        >
            {/* Toolbar */}
            <div className="flex flex-wrap gap-2 items-center px-2 pb-2 border-b border-gray-200 dark:border-gray-600">
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="太字 (Bold)"
                    onClick={() => editor?.chain().focus().toggleBold().run()}
                    disabled={!editor?.can().chain().focus().toggleBold().run()}
                    className={editor?.isActive('bold') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    B
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="斜体 (Italic)"
                    onClick={() => editor?.chain().focus().toggleItalic().run()}
                    disabled={!editor?.can().chain().focus().toggleItalic().run()}
                    className={editor?.isActive('italic') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    I
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="下線 (Underline)"
                    onClick={() => editor?.chain().focus().toggleUnderline().run()}
                    disabled={!editor?.can().chain().focus().toggleUnderline().run()}
                    className={editor?.isActive('underline') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    U
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="打消し (Strikethrough)"
                    onClick={() => editor?.chain().focus().toggleStrike().run()}
                    disabled={!editor?.can().chain().focus().toggleStrike().run()}
                    className={editor?.isActive('strike') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    S
                </Button>
                <span className="mx-1 text-gray-300">|</span>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="見出し H1"
                    onClick={() => editor?.chain().focus().toggleHeading({ level: 1 }).run()}
                    className={
                        editor?.isActive('heading', { level: 1 })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    H1
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="見出し H2"
                    onClick={() => editor?.chain().focus().toggleHeading({ level: 2 }).run()}
                    className={
                        editor?.isActive('heading', { level: 2 })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    H2
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="見出し H3"
                    onClick={() => editor?.chain().focus().toggleHeading({ level: 3 }).run()}
                    className={
                        editor?.isActive('heading', { level: 3 })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    H3
                </Button>
                <span className="mx-1 text-gray-300">|</span>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="箇条書き (Bullet List)"
                    onClick={() => editor?.chain().focus().toggleBulletList().run()}
                    className={editor?.isActive('bulletList') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    <HiOutlineViewList />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="番号付きリスト (Ordered List)"
                    onClick={() => editor?.chain().focus().toggleOrderedList().run()}
                    className={
                        editor?.isActive('orderedList') ? 'bg-gray-200 dark:bg-gray-600' : ''
                    }
                >
                    <HiOutlineHashtag />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="引用 (Blockquote)"
                    onClick={() => editor?.chain().focus().toggleBlockquote().run()}
                    className={editor?.isActive('blockquote') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    <HiOutlineChatAlt2 />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="コードブロック"
                    onClick={() => editor?.chain().focus().toggleCodeBlock().run()}
                    className={editor?.isActive('codeBlock') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    <HiOutlineCode />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="左揃え"
                    onClick={() => editor?.chain().focus().setTextAlign('left').run()}
                    className={
                        editor?.isActive({ textAlign: 'left' })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    <CiTextAlignLeft />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="中央揃え"
                    onClick={() => editor?.chain().focus().setTextAlign('center').run()}
                    className={
                        editor?.isActive({ textAlign: 'center' })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    <CiTextAlignCenter />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="右揃え"
                    onClick={() => editor?.chain().focus().setTextAlign('right').run()}
                    className={
                        editor?.isActive({ textAlign: 'right' })
                            ? 'bg-gray-200 dark:bg-gray-600'
                            : ''
                    }
                >
                    <CiTextAlignRight />
                </Button>
                <span className="mx-1 text-gray-300">|</span>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="リンク設定"
                    onClick={() => {
                        const prev = editor?.getAttributes('link')?.href || ''
                        const url = window.prompt('リンクURLを入力', prev)
                        if (url === null) return
                        if (url === '') {
                            editor?.chain().focus().unsetLink().run()
                            return
                        }
                        editor?.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
                    }}
                    className={editor?.isActive('link') ? 'bg-gray-200 dark:bg-gray-600' : ''}
                >
                    <HiOutlineLink />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="リンク解除"
                    onClick={() => editor?.chain().focus().unsetLink().run()}
                    disabled={!editor?.isActive('link')}
                >
                    <HiOutlineX />
                </Button>
                <span className="mx-1 text-gray-300">|</span>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="元に戻す (Undo)"
                    onClick={() => editor?.chain().focus().undo().run()}
                    disabled={!editor?.can().chain().focus().undo().run()}
                >
                    <HiOutlineArrowNarrowLeft />
                </Button>
                <Button
                    size="xs"
                    outline
                    color="light"
                    title="やり直し (Redo)"
                    onClick={() => editor?.chain().focus().redo().run()}
                    disabled={!editor?.can().chain().focus().redo().run()}
                >
                    <HiOutlineArrowNarrowRight />
                </Button>
                {!readOnly && isAiUse && !isNotAiUse && (
                    <>
                        <span className="mx-1 text-gray-300">|</span>
                        <Button
                            size="xs"
                            outline
                            color="light"
                            title="AI文章添削"
                            onClick={() =>
                                proofreadingRef?.current?.show(
                                    editor?.getHTML(),
                                    editor?.getText(),
                                    true
                                )
                            }
                            disabled={
                                proofreadingRef?.current?.isProofreading() ||
                                !editor?.getText()?.trim()
                            }
                            className={
                                proofreadingRef?.current?.isProofreading() ? 'opacity-50' : ''
                            }
                        >
                            <>
                                <HiOutlineLightBulb
                                    className="inline"
                                    style={{ marginRight: '2px' }}
                                />
                                AIアシスト
                            </>
                        </Button>
                    </>
                )}
            </div>

            {/* Editor */}
            <div style={{ minHeight }} className="p-2">
                <EditorContent editor={editor} className={twMerge('', editorClassName)} />
            </div>
            {!isNotAiUse && (
                <ProofReading
                    ref={proofreadingRef}
                    onProofread={(text) => {
                        editor.commands.setContent(text, false)
                        onChange(text)
                    }}
                />
            )}
        </div>
    )
}
