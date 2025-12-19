import { Button } from '@/utils/components/ui/button'
import { Modal, ModalBody, ModalHeader } from '@/utils/components/ui/modal'
import { Spinner } from '@/utils/components/ui/spinner'
import { Accordion, AccordionContent, AccordionPanel, AccordionTitle } from 'flowbite-react'
import { forwardRef, useCallback, useEffect, useImperativeHandle, useState } from 'react'
import config from '@/config/configLoader'
import { useAxios } from '@/utils/hooks/useAxios'
import { CodeEditor } from '@/utils/components/ui/form/CodeEditor'

export const CategoriesApiPreviewModal = forwardRef(({ modelData }, ref) => {
    const [show, setShow] = useState(false)
    const endpointUrl = `${config.frontEndpointUrl}${modelData.alias}/categories`
    const { data, loading, sendRequest } = useAxios()

    useImperativeHandle(ref, () => ({
        show: () => setShow(true),
        hide: () => setShow(false),
    }))

    useEffect(() => {
        if (modelData && !data) {
            getResponseData()
        }
    }, [modelData])

    // レスポンスデータ取得
    const getResponseData = useCallback(() => {
        sendRequest({
            method: 'get',
            url: endpointUrl,
            headers: {
                'X-CMS-API-KEY': modelData.api_header_key,
            },
        })
    }, [endpointUrl])

    return (
        <>
            <Modal show={show} onClose={() => setShow(false)} size="5xl">
                <ModalHeader>APIプレビュー</ModalHeader>
                <ModalBody>
                    <h4 className="text-lg font-bold">リクエスト</h4>
                    <table className="table-auto w-full dark:text-white mt-2">
                        <thead>
                            <tr>
                                <th
                                    className="text-left p-2 bg-gray-100 dark:bg-gray-800"
                                    colSpan={2}
                                >
                                    API URL
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="w-1/5 p-2">GET</td>
                                <td className="w-4/5 p-2">{endpointUrl}</td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th
                                    className="text-left p-2 bg-gray-100 dark:bg-gray-800"
                                    colSpan={2}
                                >
                                    Headers
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td className="p-2">X-CMS-API-KEY</td>
                                <td className="p-2">{modelData.api_header_key}</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr className="my-4" />
                    <h4 className="text-lg font-bold mt-4">レスポンス</h4>
                    <Accordion className="mt-2" alwaysOpen={true} collapseAll>
                        <AccordionPanel>
                            <AccordionTitle>レスポンス</AccordionTitle>
                            <AccordionContent>
                                <table className="table-auto w-full dark:text-white mt-2 border-collapse">
                                    <tbody>
                                        <tr className="border-b border-gray-200 dark:border-gray-700">
                                            <td className="w-1/5 p-2">sucess</td>
                                            <td className="w-4/5 p-2">取得結果</td>
                                        </tr>
                                        <tr className="border-b border-gray-200 dark:border-gray-700">
                                            <td className="w-1/5 p-2">timestamp</td>
                                            <td className="w-4/5 p-2">取得時間</td>
                                        </tr>
                                        <tr>
                                            <td className="w-1/5 p-2">contents</td>
                                            <td className="w-4/5 p-2">カテゴリデータ</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </AccordionContent>
                        </AccordionPanel>
                        <AccordionPanel>
                            <AccordionTitle>プレビュー</AccordionTitle>
                            <AccordionContent>
                                <Button
                                    size="xs"
                                    outline
                                    onClick={(e) => {
                                        e.stopPropagation()
                                        getResponseData()
                                    }}
                                    className="mb-2"
                                >
                                    取得
                                </Button>
                                {loading && <Spinner />}
                                {!loading && data && (
                                    <CodeEditor
                                        value={JSON.stringify(data, null, 2)}
                                        language="json"
                                        readOnly={true}
                                        rows={15}
                                        className="mt-2"
                                    />
                                )}
                                {!loading && !data && (
                                    <div className="text-center text-gray-500">
                                        データを取得して下さい
                                    </div>
                                )}
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                </ModalBody>
            </Modal>
        </>
    )
})
