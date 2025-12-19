import { config } from '@/mod/action_log/utils/config'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { useEffect, useRef, useState } from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { AdvanceSearchButton, Button } from '@/utils/components/ui/button'
import dayjs from 'dayjs'
import { Modal, ModalHeader } from '@/utils/components/ui/modal'
import { ModalBody, Table, TableBody, TableCell, TableRow } from 'flowbite-react'
import { Spinner } from '@/utils/components/ui/spinner'
import { FormBuilder, FormGroup, Label } from '@/utils/components/ui/form'
import { Col, Row } from '@/utils/components/ui/grid'

export const Index = () => {
    const breads = [{ name: config.name }]
    const column = [
        { key: 'user', label: '操作ユーザー', _props: { style: { width: '15%' } } },
        { key: 'path', label: 'パス', _props: { style: { width: '15%' } } },
        { key: 'method', label: 'メソッド', _props: { style: { width: '5%' } } },
        { key: 'http_status', label: 'ステータス', _props: { style: { width: '5%' } } },
        { key: 'created_at', label: '実行日時', _props: { style: { width: '10%' } } },
        { key: 'duration', label: '実行時間', _props: { style: { width: '10%' } } },
        { key: 'ip', label: 'ipアドレス', _props: { style: { width: '10%' } } },
        { key: 'user_agent', label: 'ユーザーエージェント', _props: { style: { width: '20%' } } },
        { key: 'btns', label: '', sortable: false, _props: { style: { width: '12%' } } },
    ]
    const [showModal, setShowModal] = useState(false)
    const idRef = useRef(null)
    const { loading, data, sendRequest } = useAxios()

    useEffect(() => {
        if (!idRef.current) return
        sendRequest({ method: 'get', url: `${config.end_point}/${idRef.current}` })
    }, [idRef.current])

    const renderParams = (value, key) => {
        if (value === null || value === undefined || typeof value !== 'object') {
            return (
                <dd key={key}>
                    {key}: {String(value)}
                </dd>
            )
        }
        const entries = Array.isArray(value) ? value.map((v, i) => [i, v]) : Object.entries(value)
        return (
            <div key={key}>
                {key && <dd>{key}</dd>}
                <dl className="ms-4">
                    {entries.map(([k, v]) => renderParams(v, `${key ? `${key}.` : ''}${k}`))}
                </dl>
            </div>
        )
    }

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config,
                    columns: column,
                    isNew: false,
                    isEdit: false,
                    isDelete: false,
                    addScopedColumns: {
                        created_at: (item) => {
                            const date = dayjs(item.created_at)
                            return (
                                <td className="text-end pe-5">
                                    {date.format('YYYY-MM-DD')}
                                    <br />
                                    {date.format('HH:mm:ss')}
                                </td>
                            )
                        },
                        duration: (item) => {
                            return (
                                <td className="text-end pe-5">
                                    {Math.round(item.duration * 1000) / 1000} 秒
                                </td>
                            )
                        },
                        user: (item) => {
                            return <td className="text-end pe-5">{item.user?.name}</td>
                        },
                        user_agent: (item) => {
                            return (
                                <td className="pe-5" title={item.user_agent}>
                                    {item.user_agent && item.user_agent.length > 30
                                        ? item.user_agent.slice(0, 30) + '...'
                                        : item.user_agent}
                                </td>
                            )
                        },
                        btns: (item) => {
                            return (
                                <td>
                                    <Button
                                        size="xs"
                                        color="blue"
                                        onClick={() => {
                                            idRef.current = item.id
                                            setShowModal(true)
                                        }}
                                        outline
                                    >
                                        詳細
                                    </Button>
                                </td>
                            )
                        },
                    },
                    AdvancedSearchPanel: ({ values = {}, onChange, onApply, onClear, close }) => {
                        return (
                            <>
                                <FormGroup>
                                    <Label htmlFor="_created_at">実行日時</Label>
                                    <Row cols={12}>
                                        <Col col={5}>
                                            <FormBuilder
                                                id="created_at_start"
                                                name="created_at_start"
                                                formType="date"
                                                defaultValue={values.created_at_start ?? ''}
                                                onChange={(value) =>
                                                    onChange('created_at_start', value)
                                                }
                                            />
                                        </Col>
                                        <Col col={2} className="flex items-center justify-center">
                                            〜
                                        </Col>
                                        <Col col={5}>
                                            <FormBuilder
                                                id="created_at_end"
                                                name="created_at_end"
                                                formType="date"
                                                defaultValue={values.created_at_end ?? ''}
                                                onChange={(value) =>
                                                    onChange('created_at_end', value)
                                                }
                                            />
                                        </Col>
                                    </Row>
                                </FormGroup>
                                <AdvanceSearchButton onClear={onClear} onApply={onApply} />
                            </>
                        )
                    },
                }}
            />
            <Modal show={showModal} onClose={() => setShowModal(false)} size="">
                <ModalHeader>詳細</ModalHeader>
                <ModalBody>
                    {loading ? (
                        <div className="flex justify-center items-center h-40">
                            <Spinner />
                        </div>
                    ) : (
                        <>
                            <Table striped>
                                <TableBody>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">
                                            操作ユーザー
                                        </TableCell>
                                        <TableCell>{data?.payload.data.user?.name}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">パス</TableCell>
                                        <TableCell>{data?.payload.data.path}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">メソッド</TableCell>
                                        <TableCell>{data?.payload.data.method}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">
                                            送信データ
                                        </TableCell>
                                        <TableCell>
                                            {data?.payload.data.params && (
                                                <>
                                                    <dl>
                                                        {Object.entries(
                                                            data?.payload.data.params
                                                        ).map(([key, value]) =>
                                                            renderParams(value, key)
                                                        )}
                                                    </dl>
                                                </>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">
                                            ステータス
                                        </TableCell>
                                        <TableCell>{data?.payload.data.http_status}</TableCell>
                                    </TableRow>
                                    {data?.payload.data.http_status >= 300 && (
                                        <>
                                            <TableRow>
                                                <TableCell className="font-bold w-1/6">
                                                    メッセージ
                                                </TableCell>
                                                <TableCell>{data?.payload.data.message}</TableCell>
                                            </TableRow>
                                        </>
                                    )}
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">実行日時</TableCell>
                                        <TableCell>
                                            {dayjs(data?.payload.data.created_at).format(
                                                'YYYY-MM-DD HH:mm:ss'
                                            )}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">実行時間</TableCell>
                                        <TableCell>
                                            {Math.round(data?.payload.data.duration * 1000) / 1000}{' '}
                                            秒
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">
                                            IPアドレス
                                        </TableCell>
                                        <TableCell>{data?.payload.data.ip}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-bold w-1/6">
                                            ユーザーエージェント
                                        </TableCell>
                                        <TableCell>{data?.payload.data.user_agent}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </>
                    )}
                </ModalBody>
            </Modal>
        </>
    )
}
