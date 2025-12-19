import { forwardRef, useCallback, useEffect, useImperativeHandle, useState } from 'react'
import { Modal, ModalHeader, ModalBody } from '@/utils/components/ui/modal'
import config from '@/config/configLoader'
import { useAxios } from '@/utils/hooks/useAxios'
import {
    Accordion,
    AccordionContent,
    AccordionPanel,
    AccordionTitle,
    Spinner,
} from 'flowbite-react'
import { Button } from '@/utils/components/ui/button'
import { TabItem, Tabs } from '@/utils/components/ui/tabs'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { CodeEditor } from '@/utils/components/ui/form/CodeEditor'

export const DetailApiPreviewModal = forwardRef(({ modelData }, ref) => {
    const [show, setShow] = useState(false)
    const { data, loading, sendRequest } = useAxios()
    const { data: markupData, loading: markupLoading, sendRequest: sendMarkupRequest } = useAxios()
    const endpointUrl = `${config.frontEndpointUrl}${modelData.alias}/`
    const [id, setId] = useState(null)
    const [responseData, setResponseData] = useState(null)
    const { facilityOptions } = useCompanyFacility()

    useImperativeHandle(
        ref,
        () => ({
            show: (id) => {
                setShow(true)
                setId(id)
            },
            hide: () => setShow(false),
        }),
        []
    )

    useEffect(() => {
        if (id && !responseData) {
            getResponseData()
            getMarkupResponseData()
        }
    }, [id])

    // レスポンスデータ取得
    const getResponseData = useCallback(async () => {
        const response = await sendRequest({
            method: 'get',
            url: `${endpointUrl}${id}`,
            headers: {
                'X-CMS-API-KEY': modelData.api_header_key,
            },
        })
        setResponseData(response.data)
    }, [id])

    const getMarkupResponseData = useCallback(async () => {
        const response = await sendMarkupRequest({
            method: 'get',
            url: `${endpointUrl}markup/${id}`,
            headers: {
                'X-CMS-API-KEY': modelData.api_header_key,
            },
        })
    }, [id])

    return (
        <Modal
            show={show}
            onClose={() => {
                setShow(false)
                setResponseData(null)
            }}
            size="5xl"
        >
            <ModalHeader>APIプレビュー</ModalHeader>
            <ModalBody>
                <Tabs>
                    <TabItem title={'記事'} active>
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
                                    <td className="w-4/5 p-2">{`${endpointUrl}${id}`}</td>
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
                            <thead>
                                <tr>
                                    <th
                                        className="text-left p-2 bg-gray-100 dark:bg-gray-800"
                                        colSpan={2}
                                    >
                                        URL Parameter
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td className="p-2">id</td>
                                    <td>記事ID</td>
                                </tr>
                            </tbody>
                            <thead>
                                <tr>
                                    <th
                                        className="text-left p-2 bg-gray-100 dark:bg-gray-800"
                                        colSpan={2}
                                    >
                                        Query Parameter
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr className="border-b border-gray-200 dark:border-gray-700">
                                    <td className="p-2">criteria</td>
                                    <td>
                                        次へ、前へページングの検索ワード
                                        (使用例：criteria[freeword]=test)
                                        <br />
                                        <br />
                                        施設ごとの検索ワードは、施設エイリアスを指定してください。
                                        <br />
                                        (使用例：criteria[freeword]=test&facility_alias=facility1)
                                        <br />
                                        施設エイリアスは、
                                        {facilityOptions
                                            .map(
                                                (option) => option.value + '(' + option.label + ')'
                                            )
                                            .join(', ')}
                                    </td>
                                </tr>
                                <tr className="border-b border-gray-200 dark:border-gray-700">
                                    <td className="p-2 border-b border-gray-200 dark:border-gray-700">
                                        sort
                                    </td>
                                    <td>並び順対象項目 (使用例：sort=created_at)</td>
                                </tr>
                                <tr className="border-b border-gray-200 dark:border-gray-700">
                                    <td className="p-2 border-b border-gray-200 dark:border-gray-700">
                                        direction
                                    </td>
                                    <td>並び順 (使用例：direction=desc)</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 className="text-lg font-bold mt-4">レスポンス</h4>
                        <Accordion className="mt-2" alwaysOpen={true} collapseAll>
                            <AccordionPanel>
                                <AccordionTitle className="text-left p-2 bg-gray-100 dark:bg-gray-800 font-bold">
                                    レスポンスデータ
                                </AccordionTitle>
                                <AccordionContent>
                                    <table className="table-auto w-full dark:text-white border-collapse">
                                        <tbody>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">sucess</td>
                                                <td className="w-4/5 p-2">取得結果</td>
                                            </tr>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">timestamp</td>
                                                <td className="w-4/5 p-2">取得時間</td>
                                            </tr>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">content</td>
                                                <td className="w-4/5 p-2">記事データ</td>
                                            </tr>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">sibLings</td>
                                                <td className="w-4/5 p-2">前後の記事データ</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </AccordionContent>
                            </AccordionPanel>

                            <AccordionPanel>
                                <AccordionTitle className="text-left p-2 bg-gray-100 dark:bg-gray-800 font-bold">
                                    プレビュー
                                </AccordionTitle>
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
                                    {!loading && responseData && (
                                        <CodeEditor
                                            value={JSON.stringify(responseData, null, 2)}
                                            language="json"
                                            readOnly={true}
                                            rows={15}
                                            className="mt-2"
                                        />
                                    )}
                                    {!loading && !responseData && (
                                        <div className="text-center text-gray-500">
                                            <div className="text-center text-gray-500">
                                                データを取得して下さい
                                            </div>
                                        </div>
                                    )}
                                </AccordionContent>
                            </AccordionPanel>
                        </Accordion>
                    </TabItem>

                    <TabItem title={'構造化マークアップ'}>
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
                                    <td className="w-4/5 p-2">{`${endpointUrl}markup/${id}`}</td>
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
                            <thead>
                                <tr>
                                    <th
                                        className="text-left p-2 bg-gray-100 dark:bg-gray-800"
                                        colSpan={2}
                                    >
                                        URL Parameter
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td className="p-2">id</td>
                                    <td>記事ID</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr className="my-4" />

                        <h4 className="text-lg font-bold mt-4">レスポンス</h4>
                        <Accordion className="mt-2" alwaysOpen={true} collapseAll>
                            <AccordionPanel>
                                <AccordionTitle className="text-left p-2 bg-gray-100 dark:bg-gray-800 font-bold">
                                    レスポンスデータ
                                </AccordionTitle>
                                <AccordionContent>
                                    <table className="table-auto w-full dark:text-white border-collapse">
                                        <tbody>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">sucess</td>
                                                <td className="w-4/5 p-2">取得結果</td>
                                            </tr>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">timestamp</td>
                                                <td className="w-4/5 p-2">取得時間</td>
                                            </tr>
                                            <tr className="border-b border-gray-200 dark:border-gray-700">
                                                <td className="w-1/5 p-2">contents</td>
                                                <td className="w-4/5 p-2">
                                                    構造化マークアップデータ
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </AccordionContent>
                            </AccordionPanel>

                            <AccordionPanel>
                                <AccordionTitle className="text-left p-2 bg-gray-100 dark:bg-gray-800 font-bold">
                                    プレビュー
                                </AccordionTitle>
                                <AccordionContent>
                                    <Button
                                        size="xs"
                                        outline
                                        onClick={(e) => {
                                            e.stopPropagation()
                                            getMarkupResponseData()
                                        }}
                                        className="mb-2"
                                    >
                                        取得
                                    </Button>
                                    {markupLoading && <Spinner />}
                                    {!markupLoading && markupData && (
                                        <CodeEditor
                                            value={JSON.stringify(markupData, null, 2)}
                                            language="json"
                                            readOnly={true}
                                            rows={15}
                                            className="mt-2"
                                        />
                                    )}
                                    {!markupLoading && !markupData && (
                                        <div className="text-center text-gray-500">
                                            <div className="text-center text-gray-500">
                                                データを取得して下さい
                                            </div>
                                        </div>
                                    )}
                                </AccordionContent>
                            </AccordionPanel>
                        </Accordion>
                    </TabItem>
                </Tabs>
            </ModalBody>
        </Modal>
    )
})
