import { closestCenter, DndContext, PointerSensor, useSensor, useSensors } from '@dnd-kit/core'
import { restrictToVerticalAxis } from '@dnd-kit/modifiers'
import {
    arrayMove,
    SortableContext,
    useSortable,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable'
import { useEffect, useRef, useState } from 'react'
import { HiMenu } from 'react-icons/hi'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '@/utils/components/ui/spinner'
import { Select } from '@/utils/components/ui/form/Select'
import { Button } from '@/utils/components/ui/button'
import { v4 as uuidv4 } from 'uuid'
import { CSS } from '@dnd-kit/utilities'
import { FormComp } from '@/utils/components/ui/form'

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

    return (
        <div
            ref={setNodeRef}
            style={style}
            className={`flex gap-2 mb-2 items-center ${isDragging ? 'opacity-80' : ''}`}
        >
            {!readonly && (
                <span
                    className="cursor-grab text-gray-400 hover:text-gray-600"
                    style={{ touchAction: 'none' }}
                    {...attributes}
                    {...listeners}
                >
                    <HiMenu size={20} />
                </span>
            )}
            {children}
        </div>
    )
}

export const CustomBlock = ({
    defaultValue = [],
    options,
    onChange,
    validationErrors = {},
    id,
    readonly = false,
    disabled = false,
    contentConfig = null, // Form.jsxから渡される
    ...props
}) => {
    // カスタムブロック用
    const { parent_block_id, endpoint } = options
    const isGetCustomBlock = parent_block_id && endpoint
    const { data: customBlockData, loading, sendRequest: fetchCustomBlock } = useAxios()
    const [blockOptions, setBlockOptions] = useState([])
    const selectedRef = useRef(null)

    const [blocks, setBlocks] = useState([])
    const defaultValueRef = useRef(defaultValue) // defaultValueの前回の値を保存
    const blocksInitializedRef = useRef(false) // blocksが初期化済みかどうかを追跡
    const blocksRef = useRef([]) // blocksの前回の値を保存
    const contentConfigRef = useRef(contentConfig) // contentConfigの前回の値を保存
    const customBlockDataRef = useRef(null) // customBlockDataの前回の値を保存
    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: { distance: 5 },
        })
    )

    // defaultValueRefを更新（defaultValueが変更されたとき）
    useEffect(() => {
        defaultValueRef.current = defaultValue
    }, [defaultValue])

    // カスタムブロック一覧取得
    useEffect(() => {
        if (isGetCustomBlock) {
            ;(async () => {
                await fetchCustomBlock({
                    method: 'get',
                    url: endpoint,
                    params: {
                        criteria: { parent_block_id },
                    },
                })
            })()
        }
    }, [parent_block_id])

    // カスタムブロックオプションの初期化
    useEffect(() => {
        if (isGetCustomBlock && customBlockData?.payload?.data) {
            // customBlockDataが実際に変更されたかどうかを確認
            const customBlockDataChanged =
                JSON.stringify(customBlockDataRef.current) !== JSON.stringify(customBlockData)

            // contentConfigが実際に変更されたかどうかを確認
            const contentConfigChanged =
                JSON.stringify(contentConfigRef.current) !== JSON.stringify(contentConfig)

            // customBlockDataまたはcontentConfigが変更された場合のみ処理
            if (!customBlockDataChanged && !contentConfigChanged && blocksInitializedRef.current) {
                return
            }

            // 値を更新
            customBlockDataRef.current = customBlockData
            contentConfigRef.current = contentConfig

            const newBlockOptions = customBlockData.payload.data.map((val) => {
                const form = {
                    formType: val.field_type,
                    field_id: val.field_id,
                    custom_field_id: val?.custom_field_id,
                    block_children_id: val.id,
                    title: val.name,
                    placeholder: val.placeholder,
                    help_text: val.help_text,
                    items: val?.choices ?? [],
                    required: val.is_required || val.is_required === 1 ? true : false,
                    [val.field_id]: {},
                }

                // content_referenceタイプの場合はエンドポイントを設定
                if (val.field_type === 'content_reference') {
                    // 参照先ContentModelの情報を渡す
                    form.contentReferenceModel = val?.content_reference || null
                }

                // richtextタイプの場合はAI添削用の情報を設定
                if (val.field_type === 'richtext' && contentConfig) {
                    form.contentConfig = contentConfig
                    // contentEndpointはcontentConfigから取得
                    if (contentConfig?.end_point) {
                        form.contentEndpoint = contentConfig.end_point
                    }
                }

                return {
                    label: val.name,
                    value: val.id,
                    form,
                }
            })

            setBlockOptions(newBlockOptions)

            // defaultValueからblocksを初期化（初回のみ、またはcustomBlockDataが変更された場合）
            if (!blocksInitializedRef.current || customBlockDataChanged) {
                // 初回はdefaultValueRef.currentを使用（初期化時に設定されている）
                const currentDefaultValue = defaultValueRef.current

                const newBlocks = Array.isArray(currentDefaultValue)
                    ? currentDefaultValue.map((value) => {
                          const block = newBlockOptions.find((v) => v.value == value.field_id)
                          return {
                              ...block?.form,
                              id: value.block_seq_id,
                              values: value.values,
                          }
                      })
                    : []

                setBlocks(newBlocks)
                blocksRef.current = newBlocks
                blocksInitializedRef.current = true
            }
        }
    }, [customBlockData, contentConfig, isGetCustomBlock])

    // defaultValueの変更を検知してblocksを更新
    useEffect(() => {
        if (isGetCustomBlock && blockOptions.length > 0 && blocksInitializedRef.current) {
            // defaultValueが実際に変更されたかどうかを確認
            const defaultValueChanged =
                JSON.stringify(defaultValueRef.current) !== JSON.stringify(defaultValue)

            if (!defaultValueChanged) {
                // defaultValueが変更されていない場合は何もしない
                return
            }

            // defaultValueを更新
            defaultValueRef.current = defaultValue

            const newBlocks = Array.isArray(defaultValue)
                ? defaultValue.map((value) => {
                      const block = blockOptions.find((v) => v.value == value.field_id)
                      return {
                          ...block?.form,
                          id: value.block_seq_id,
                          values: value.values,
                      }
                  })
                : []

            // 既存のblocksと比較して変更がある場合のみ更新（無限ループを防ぐ）
            const blocksChanged = JSON.stringify(blocksRef.current) !== JSON.stringify(newBlocks)
            if (blocksChanged) {
                setBlocks(newBlocks)
                blocksRef.current = newBlocks
            }
        }
    }, [defaultValue, blockOptions, isGetCustomBlock])

    // ブロック追加
    const addBlock = () => {
        if (selectedRef.current) {
            const newBlock = {
                ...selectedRef.current.form,
                id: uuidv4(),
                values: {},
            }

            if (selectedRef.current.form.formType !== 'custom_field') {
                newBlock.values[selectedRef.current.form.field_id] = ''
            }

            const updated = [...blocks, newBlock]
            setBlocks(updated)
            blocksRef.current = updated
            onChangeValue(updated)
        }
    }

    // ブロック削除
    const removeBlock = (index) => {
        const newBlock = blocks.filter((_, i) => i !== index)
        setBlocks(newBlock)
        blocksRef.current = newBlock
        onChangeValue(newBlock)
    }

    // ブロック編集
    const updateField = (index, key, val) => {
        const newBlock = blocks.map((block, i) =>
            i === index ? { ...block, values: { ...block.values, [key]: val } } : block
        )
        setBlocks(newBlock)
        blocksRef.current = newBlock
        onChangeValue(newBlock)
    }

    const ids = blocks.map((c) => c.id)

    // 並び替え
    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = blocks.findIndex((c) => c.id === active.id)
        const newIndex = blocks.findIndex((c) => c.id === over.id)
        const updated = arrayMove(blocks, oldIndex, newIndex)
        setBlocks(updated)
        blocksRef.current = updated

        onChangeValue(updated)
    }

    const onChangeValue = (updated) => {
        const values = []
        updated.map((block) => {
            values.push({
                values: block.values,
                block_seq_id: block.id,
                field_id: block.block_children_id,
            })
        })

        onChange && onChange(values)
    }

    if (isGetCustomBlock && loading) {
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
                    <div className="ms-6">
                        {blocks.map((block, index) => {
                            return (
                                <SortableRow
                                    key={block.id}
                                    id={block.id}
                                    readonly={readonly || disabled}
                                >
                                    <FormComp
                                        item={{
                                            ...block,
                                            defaultValue: block.values[block.field_id],
                                            validationKey: `${id}.${index}.values.${block.field_id}`,
                                            onChange: (val) => {
                                                updateField(index, block.field_id, val)
                                            },
                                            // contentConfigをリッチテキストフィールドに渡す
                                            contentConfig: contentConfig,
                                        }}
                                        validationErrors={validationErrors}
                                        index={index}
                                        readonly={readonly || disabled}
                                    />
                                    {!readonly && !disabled && (
                                        <Button
                                            size="sm"
                                            color={'red'}
                                            outline
                                            onClick={() => removeBlock(index)}
                                            className="ms-4"
                                        >
                                            削除
                                        </Button>
                                    )}
                                </SortableRow>
                            )
                        })}
                    </div>
                    {!readonly && !disabled && (
                        <div className="mt-4">
                            <div className="flex items-center gap-2">
                                <Select
                                    placeholder="ブロックを選択"
                                    items={blockOptions}
                                    onChange={(val) => {
                                        selectedRef.current = blockOptions.find(
                                            (v) => v.value == val
                                        )
                                    }}
                                    className="w-120"
                                />
                                <Button size="xs" outline onClick={addBlock}>
                                    ＋ブロック追加
                                </Button>
                            </div>
                        </div>
                    )}
                </SortableContext>
            </DndContext>
        </>
    )
}
