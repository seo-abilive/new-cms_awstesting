import { forwardRef, useEffect, useImperativeHandle, useState } from 'react'
import { Button } from '../button'
import { HiOutlineXCircle } from 'react-icons/hi'
import { useAxios } from '@/utils/hooks/useAxios'
import { Modal, ModalBody, ModalFooter, ModalHeader } from '../modal'
import { Col, Row } from '../grid'
import { Textarea } from '../form/Textarea'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { Radio } from '../form/Radio'
import { AI_PROMPT } from '@/utils/const'
/**
 * AI添削コンポーネント
 */
export const ProofReading = forwardRef(({ onProofread = () => {} }, ref) => {
    const [show, setShow] = useState(false)
    const [showResult, setShowResult] = useState(false)
    const [proofreadText, setProofreadText] = useState('')
    const [isProofreading, setIsProofreading] = useState(false)
    const { sendRequest } = useAxios()
    const [text, setText] = useState('')
    const [plainText, setPlainText] = useState('')
    const [isHtml, setIsHtml] = useState(false)
    const [selectedPrompt, setSelectedPrompt] = useState(AI_PROMPT[0].value)
    const [prompt, setPrompt] = useState('')
    const { company, isAiUse } = useCompanyFacility()

    useImperativeHandle(
        ref,
        () => ({
            show: (text, plainText, isHtml) => {
                setShowResult(false)
                setProofreadText('')
                setText(text)
                setPlainText(plainText)
                setIsHtml(isHtml)
                setShow(true)
            },
            hide: () => {
                setShow(false)
                setShowResult(false)
                setProofreadText('')
            },
            isProofreading: () => isProofreading,
            isAiUse: () => isAiUse,
        }),
        []
    )

    // endpoint生成
    const getProofreadEndpoint = () => {
        return `${company?.alias}/content/ai-proofread`
    }
    const resolvedProofreadEndpoint = getProofreadEndpoint()

    // 添削処理
    const handleProofread = async () => {
        if (!resolvedProofreadEndpoint) return
        if (!text || text.trim() === '' || text === '<p></p>' || !plainText.trim()) {
            alert('添削する文章がありません')
            return
        }

        setIsProofreading(true)
        try {
            const response = await sendRequest({
                method: 'POST',
                url: resolvedProofreadEndpoint,
                data: { prompt: selectedPrompt, prompt_text: prompt, text, is_html: isHtml },
            })

            if (response.success && response.data?.payload?.data) {
                const { original, proofread } = response.data.payload.data
                // モーダルで結果を表示
                setProofreadText(proofread)
                setShowResult(true)
            } else {
                const errorMsg = response.data?.payload?.error || '添削に失敗しました'
                alert(errorMsg)
            }
        } catch (error) {
            console.error('AI添削エラー:', error)
            alert('添削処理中にエラーが発生しました')
        } finally {
            setIsProofreading(false)
        }
    }

    if (!isAiUse) return <></>

    return (
        <>
            {show && (
                <>
                    <div
                        className="fixed inset-0 z-40 bg-black/30"
                        onClick={() => setShow(false)}
                    />
                    <div className="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white dark:bg-gray-800 shadow-xl transform transition-transform duration-300 translate-x-0">
                        <div className="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 className="text-base font-semibold text-gray-500 dark:text-gray-200">
                                AIアシスト
                            </h3>
                            <Button size="xs" outline color="light" onClick={() => setShow(false)}>
                                <HiOutlineXCircle className="h-5 w-5" />
                            </Button>
                        </div>
                        <div className="p-4 overflow-y-auto">
                            <Row rows={12} className="text-gray-500 dark:text-gray-400">
                                <Col col={12}>
                                    <div className="">AIへの指示</div>
                                </Col>
                                <Col col={12} className="mt-4">
                                    <Radio
                                        id="ai-prompt"
                                        items={AI_PROMPT}
                                        defaultValue={selectedPrompt}
                                        vertical={true}
                                        onChange={(value) => setSelectedPrompt(value)}
                                    />
                                </Col>
                                {selectedPrompt === 'custom' && (
                                    <Col col={12} className="mt-4">
                                        <Textarea
                                            defaultValue={prompt}
                                            onChange={(value) => setPrompt(value)}
                                            isNotAiUse={true}
                                            placeholder="プロンプトを入力してください"
                                            rows={6}
                                        />
                                    </Col>
                                )}
                            </Row>
                            <div className="mt-4 flex gap-2 justify-end">
                                <Button
                                    size="xs"
                                    color="light"
                                    outline
                                    onClick={() => setShow(false)}
                                >
                                    クリア
                                </Button>
                                <Button
                                    size="xs"
                                    color="blue"
                                    onClick={() => handleProofread()}
                                    disabled={isProofreading}
                                >
                                    {isProofreading ? 'AI思考中...' : 'AIアシスト実行'}
                                </Button>
                            </div>
                        </div>
                    </div>
                </>
            )}
            <Modal
                show={showResult}
                onClose={() => setShowResult(false)}
                className="text-gray-500 dark:text-gray-400"
            >
                <ModalHeader>AI結果</ModalHeader>
                <ModalBody>
                    <div
                        dangerouslySetInnerHTML={{
                            __html: isHtml ? proofreadText : proofreadText.replace(/\n/g, '<br />'),
                        }}
                    />
                </ModalBody>
                <ModalFooter>
                    <Button onClick={() => setShowResult(false)} color="light" outline size="xs">
                        閉じる
                    </Button>
                    <Button
                        onClick={() => {
                            onProofread(proofreadText)
                            setShowResult(false)
                            setShow(false)
                        }}
                        size="xs"
                        color="blue"
                    >
                        反映する
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    )
})
