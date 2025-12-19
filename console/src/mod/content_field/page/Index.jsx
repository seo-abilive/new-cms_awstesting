import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { config, customConfig, fieldItem } from '@/mod/content_field/utils/config'
import { useEffect, useState } from 'react'
import { Modal, ModalBody, ModalHeader } from '@/utils/components/ui/modal'
import { Button } from '@/utils/components/ui/button'
import { Col, Row } from '@/utils/components/ui/grid'
import { useContetField } from '@/mod/content_field/utils/context/ContentFieldContext'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { useAxios } from '@/utils/hooks/useAxios'

export const Index = () => {
    const { model_id, getBreads, replacePath, config } = useContetField()
    const breads = getBreads([{ name: config.name }])
    const [showModal, setShowModal] = useState(false)
    const { navigateTo } = useNavigation()
    const [combinedFieldItems, setCombinedFieldItems] = useState(fieldItem)
    const { data: customFieldsData, sendRequest: fetchCustomFields } = useAxios()

    useEffect(() => {
        ;(async () => {
            await fetchCustomFields({
                method: 'get',
                url: replacePath(customConfig.end_point + '/resource'),
                params: {
                    criteria: { model_id: model_id },
                },
            })
        })()
    }, [model_id])

    useEffect(() => {
        if (customFieldsData?.payload?.data) {
            const customItems = customFieldsData.payload.data.map((custom) => ({
                label: custom.name,
                value: 'custom_field',
                is_custom: true,
                custom_field_id: custom.id,
                icon: null, // You can add a specific icon for custom groups here
            }))
            setCombinedFieldItems([
                ...fieldItem,
                ...customItems,
                ...[
                    {
                        label: 'カスタムブロック',
                        value: 'custom_block',
                        icon: null,
                        isChoice: false,
                    },
                ],
            ])
        }
    }, [customFieldsData])

    const columns = [
        { key: 'name', label: '名前' },
        { key: 'field_type', label: 'フィールドタイプ' },
        { key: 'actions', label: '', sortable: false, _props: { style: { width: '10%' } } },
    ]

    return (
        <>
            <ResourceIndex
                options={{
                    breads,
                    config,
                    columns,
                    baseParams: { criteria: { model_id: model_id, is_top_field: 1 } },
                    customNewAction: () => {
                        setShowModal(true)
                    },
                    addScopedColumns: {
                        field_type: (item, row, idx) => {
                            return (
                                <td>
                                    {item.field_type !== 'custom_field'
                                        ? item.field_type
                                        : item.custom_field.name}
                                </td>
                            )
                        },
                    },
                    customEditAction: (item) => {
                        const state = {
                            field_type: item.field_type,
                        }
                        if (item.field_type === 'custom_field') {
                            state.custom_field_id = item.custom_field.id
                            state.name = item.custom_field.name
                        }
                        navigateTo(`${config.path}/edit/${item.id}`, state)
                    },
                    addPageActionButtons: [
                        () => {
                            return (
                                <Button
                                    outline
                                    size="xs"
                                    onClick={() => {
                                        navigateTo(`${config.path}/custom`)
                                    }}
                                >
                                    カスタムフィールド
                                </Button>
                            )
                        },
                    ],
                    isSort: true,
                }}
            />
            <Modal show={showModal} onClose={() => setShowModal(false)}>
                <ModalHeader>フィールド追加</ModalHeader>
                <ModalBody>
                    <Row cols={12} className="gap-3">
                        {combinedFieldItems.map((field, index) => {
                            return (
                                <Col col={4} key={index}>
                                    <Button
                                        color={'dark'}
                                        outline
                                        className="w-full h-20 text-1xl"
                                        onClick={() => {
                                            const path = `${config.path}/new`
                                            const state = { field_type: field.value }
                                            if (field.is_custom) {
                                                state.custom_field_id = field.custom_field_id
                                                state.name = field.label
                                            }
                                            navigateTo(path, state)
                                        }}
                                    >
                                        {field.icon && (
                                            <>
                                                <field.icon className="me-2" />
                                            </>
                                        )}
                                        {field.label}
                                    </Button>
                                </Col>
                            )
                        })}
                    </Row>
                </ModalBody>
            </Modal>
        </>
    )
}
