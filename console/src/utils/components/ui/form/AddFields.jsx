import React, { useEffect, useRef, useState } from 'react'
import { Button } from '@/utils/components/ui/button'
import { FormBuilder, FormComp, FormGroup, Label } from '@/utils/components/ui/form'
import { HiMenu } from 'react-icons/hi'
import { v4 as uuidv4 } from 'uuid'
import { DndContext, closestCenter, PointerSensor, useSensor, useSensors } from '@dnd-kit/core'
import {
    SortableContext,
    useSortable,
    verticalListSortingStrategy,
    arrayMove,
} from '@dnd-kit/sortable'
import { CSS } from '@dnd-kit/utilities'
import { restrictToVerticalAxis } from '@dnd-kit/modifiers'
import { Modal, ModalBody, ModalFooter, ModalHeader } from '../modal'
import { Col, Row } from '../grid'
import {
    Table as FTable,
    TableHead,
    TableHeadCell,
    TableBody,
    TableRow,
    TableCell,
} from 'flowbite-react'
import { getChoice } from '../../../common'
import { useAxios } from '../../../hooks/useAxios'
import { Spinner } from '../spinner'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

const SortableRow = ({ id, handle, children, readonly = false }) => {
    const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({
        id,
        disabled: readonly,
    })
    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        background: isDragging ? '#f3f4f6' : undefined,
        opacity: isDragging ? 0.8 : 1,
    }

    // Render <TableRow> instead of <tr>
    return (
        <TableRow ref={setNodeRef} style={style} className={isDragging ? 'opacity-80' : ''}>
            {/* <TableCell> for drag handle */}
            {!readonly && (
                <TableCell>
                    <span
                        className="cursor-grab text-gray-400 hover:text-gray-600"
                        style={{ touchAction: 'none' }}
                        {...attributes}
                        {...listeners}
                    >
                        <HiMenu size={20} />
                    </span>
                </TableCell>
            )}
            {children}
        </TableRow>
    )
}

export const AddFields = ({
    defaultValue = [],
    onChange,
    fieldTypes = [],
    endpoint,
    model_id,
    readonly = false,
    disabled = false,
}) => {
    const [combinedFieldItems, setCombinedFieldItems] = useState(fieldTypes)
    const { data: customFieldsData, loading, sendRequest: fetchCustomFields } = useAxios()
    const [fields, setFields] = useState(
        Array.isArray(defaultValue) ? defaultValue.map((v) => ({ ...v, id: v.id || uuidv4() })) : []
    )
    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )
    const { company_alias } = useCompanyFacility()
    // フィールド選択モーダル
    const [showSelectedModal, setShowSelectedModal] = useState(false)

    // フィールド編集モーダル
    const [showEditModal, setShowEditModal] = useState(false)
    const field = useRef(null)

    // カスタムフィールド取得
    const hasCustomField = endpoint && model_id
    useEffect(() => {
        if (hasCustomField) {
            ;(async () => {
                await fetchCustomFields({
                    method: 'get',
                    url: endpoint + '/resource',
                    params: {
                        criteria: { model_id: model_id },
                    },
                })
            })()
        }
    }, [model_id])

    useEffect(() => {
        if (hasCustomField && customFieldsData?.payload?.data) {
            const customItems = customFieldsData.payload.data.map((custom) => ({
                label: custom.name,
                value: 'custom_field',
                is_custom: true,
                custom_field_id: custom.id,
                icon: null, // You can add a specific icon for custom groups here
            }))
            setCombinedFieldItems([...fieldTypes, ...customItems])
        }
    }, [customFieldsData])

    // フィールド追加
    const addField = (fieldType) => {
        const newField = {
            id: uuidv4(),
            field_type: fieldType.value,
            label: fieldType.label,
            custom_field_id: fieldType?.custom_field_id,
            name: '',
            field_id: '',
            is_required: false,
            validates: [],
        }
        const updated = [...fields, newField]
        setFields(updated)
        onChange && onChange(updated)
        field.current = { ...fieldType, index: updated.length - 1 }
        setShowEditModal(true)
    }

    // 選択肢削除
    const removeField = (index) => {
        const newField = fields.filter((_, i) => i !== index)
        setFields(newField)
        onChange && onChange(newField)
    }

    // 選択肢編集
    const updateField = (index, key, val) => {
        const newField = fields.map((choice, i) =>
            i === index ? { ...choice, [key]: val } : choice
        )
        setFields(newField)
        onChange && onChange(newField)
    }

    const ids = fields.map((c) => c.id)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = fields.findIndex((c) => c.id === active.id)
        const newIndex = fields.findIndex((c) => c.id === over.id)
        const updated = arrayMove(fields, oldIndex, newIndex)
        setFields(updated)
        onChange && onChange(updated)
    }

    const formItem = [
        { title: '名前', id: 'name', required: true },
        { title: 'フィールドID', id: 'field_id', required: true },
        { title: 'プレイスホルダー', id: 'placeholder' },
        { title: 'ヘルプテキスト', id: 'help_text', formType: 'textarea' },
        {
            title: '必須項目',
            id: 'is_required',
            formType: 'switch',
            default: false,
        },
        {
            title: 'バリデーション',
            id: 'validates',
            formType: 'add_validates',
            default: [],
            help_text: 'このフィールドのバリデーションを設定できます。',
        },
    ]

    if (field.current?.isChoice) {
        formItem.push({ title: '選択肢', id: 'choices', formType: 'add_choices', default: [] })
    }

    if (field.current?.value === 'content_reference') {
        formItem.splice(4, 0, {
            title: '参照コンテンツ',
            id: 'content_reference_id',
            formType: 'taxonomy_select',
            endpoint: `${company_alias}/content_model/resource`,
            placeholder: '選択してください',
            isCreatable: false,
        })
    }

    if (hasCustomField && loading) {
        return <Spinner />
    }
    return (
        <>
            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragEnd={handleDragEnd}
                modifiers={[restrictToVerticalAxis]}
            >
                <SortableContext items={ids} strategy={verticalListSortingStrategy}>
                    {fields.length > 0 && (
                        <FTable>
                            <TableHead>
                                <TableRow>
                                    {!readonly && !disabled && (
                                        <TableHeadCell className="sticky left-0 z-10 w-10"></TableHeadCell>
                                    )}
                                    <TableHeadCell className="w-3xl">
                                        フィールドタイプ
                                    </TableHeadCell>
                                    <TableHeadCell className="w-3xl">名前</TableHeadCell>
                                    <TableHeadCell className="w-3xl">フィールドID</TableHeadCell>
                                    {!readonly && !disabled && (
                                        <TableHeadCell className="sticky right-0  z-10 w-60"></TableHeadCell>
                                    )}
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {fields.map((fieldItem, index) => (
                                    <SortableRow
                                        key={fieldItem.id}
                                        id={fieldItem.id}
                                        readonly={readonly || disabled}
                                    >
                                        <TableCell className="px-2 py-1">
                                            {fieldItem.field_type === 'custom_field'
                                                ? fieldItem.label
                                                : fieldItem.field_type}
                                        </TableCell>
                                        <TableCell className="px-2 py-1">
                                            {fieldItem.name || '-'}
                                        </TableCell>
                                        <TableCell className="px-2 py-1">
                                            {fieldItem.field_id || '-'}
                                        </TableCell>
                                        {!readonly && !disabled && (
                                            <TableCell className="px-2 py-1">
                                                <div className="flex gap-2">
                                                    <Button
                                                        size="xs"
                                                        onClick={() => {
                                                            field.current = {
                                                                ...getChoice(
                                                                    fieldTypes,
                                                                    fieldItem.field_type
                                                                ),
                                                                index,
                                                            }
                                                            setShowEditModal(true)
                                                        }}
                                                        outline
                                                    >
                                                        編集
                                                    </Button>
                                                    <Button
                                                        size="xs"
                                                        color="red"
                                                        onClick={() => removeField(index)}
                                                        outline
                                                    >
                                                        削除
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        )}
                                    </SortableRow>
                                ))}
                            </TableBody>
                        </FTable>
                    )}
                    {!readonly && !disabled && (
                        <div className="mt-4">
                            <Button size="xs" outline onClick={() => setShowSelectedModal(true)}>
                                ＋フィールドを追加
                            </Button>
                        </div>
                    )}
                </SortableContext>
            </DndContext>
            <Modal show={showSelectedModal} onClose={() => setShowSelectedModal(false)}>
                <ModalHeader>フィールド選択</ModalHeader>
                <ModalBody>
                    <Row cols={12} className="gap-3">
                        {combinedFieldItems.map((fieldType, index) => {
                            return (
                                <Col col={4} key={index}>
                                    <Button
                                        color={'dark'}
                                        outline
                                        className="w-full h-20 text-1xl"
                                        onClick={() => {
                                            setShowSelectedModal(false)
                                            addField(fieldType)
                                        }}
                                    >
                                        {fieldType.icon && (
                                            <>
                                                <fieldType.icon className="me-2" />
                                            </>
                                        )}
                                        {fieldType.label}
                                    </Button>
                                </Col>
                            )
                        })}
                    </Row>
                </ModalBody>
            </Modal>
            <Modal show={showEditModal} onClose={() => setShowEditModal(false)}>
                <ModalHeader>フィールド編集</ModalHeader>
                <ModalBody>
                    {formItem.map((item, index) => {
                        const { title, required = false, onFetch, ...rest } = item
                        return (
                            <FormComp
                                item={item}
                                index={index}
                                defaultValue={
                                    field.current?.index !== undefined
                                        ? fields[field.current.index]?.[rest.id]
                                        : ''
                                }
                                onChange={(value) => {
                                    if (field.current?.index !== undefined) {
                                        updateField(field.current.index, rest.id, value)
                                    }
                                }}
                            />
                        )
                    })}
                </ModalBody>
                <ModalFooter className="flex justify-end">
                    <Button
                        color={'gray'}
                        size="sm"
                        onClick={() => setShowEditModal(false)}
                        outline
                    >
                        閉じる
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    )
}
