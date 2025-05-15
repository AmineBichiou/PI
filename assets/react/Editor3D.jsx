import { Canvas } from '@react-three/fiber'
import { OrbitControls, Environment, useGLTF, Edges, Text, TransformControls, PivotControls, Html } from '@react-three/drei'
import { useRef, useState } from 'react'
import { EffectComposer, Outline, Selection } from '@react-three/postprocessing'
import { Leva, useControls } from 'leva'

function Model({ url }) {
    const { scene } = useGLTF(url)
    return <primitive object = { scene }
    />
}

function WarehouseObject({ type, position }) {
    const meshRef = useRef()
    const [hovered, setHover] = useState(false)

    let geometry;
    switch (type) {
        case 'rack':
            geometry = < boxGeometry args = {
                [2, 1.8, 1] }
            />
            break
        case 'palette':
            geometry = < boxGeometry args = {
                [1.2, 0.15, 0.8] }
            />
            break
        default:
            geometry = < boxGeometry args = {
                [1, 1, 1] }
            />
    }

    return ( <
        mesh ref = { meshRef }
        position = { position }
        onPointerOver = {
            () => setHover(true) }
        onPointerOut = {
            () => setHover(false) }
        castShadow >
        { geometry } <
        meshStandardMaterial color = { hovered ? 'hotpink' : 'orange' }
        /> <
        Edges visible = { hovered }
        scale = { 1.05 }
        renderOrder = { 1000 } >
        <
        lineBasicMaterial color = "white"
        transparent depthTest = { false }
        /> <
        /Edges> <
        /mesh>
    )
}

export function Editor3D() {
    const { bgColor, wallColor, floorColor } = useControls({
        bgColor: '#f0f4f8',
        wallColor: '#d3d3d3',
        floorColor: '#696969'
    })

    const [objects, setObjects] = useState([])

    const addObject = (type) => {
        setObjects([...objects, {
            id: Math.random().toString(36).substring(7),
            type,
            position: [0, 0, 0]
        }])
    }

    return ( <
        div style = {
            { position: 'relative', height: '800px' } } >
        <
        Leva collapsed / >
        <
        Canvas shadows camera = {
            { position: [10, 10, 10], fov: 50 } } >
        <
        color attach = "background"
        args = {
            [bgColor] }
        /> <
        ambientLight intensity = { 0.5 }
        /> <
        directionalLight position = {
            [10, 10, 5] }
        intensity = { 1 }
        castShadow shadow - mapSize - width = { 2048 }
        shadow - mapSize - height = { 2048 }
        />

        <
        Selection >
        <
        EffectComposer multisampling = { 8 }
        autoClear = { false } >
        <
        Outline blur visibleEdgeColor = "white"
        edgeStrength = { 100 }
        width = { 1000 }
        /> <
        /EffectComposer>

        <
        group position = {
            [0, -1, 0] } > { /* Sol */ } <
        mesh rotation = {
            [-Math.PI / 2, 0, 0] }
        receiveShadow >
        <
        planeGeometry args = {
            [20, 20] }
        /> <
        meshStandardMaterial color = { floorColor }
        /> <
        /mesh>

        { /* Murs */ } <
        mesh position = {
            [0, 4.5, -10] }
        castShadow >
        <
        boxGeometry args = {
            [20, 10, 0.5] }
        /> <
        meshStandardMaterial color = { wallColor }
        transparent opacity = { 0.8 }
        /> <
        /mesh>

        { /* Objets */ } {
            objects.map((obj) => ( <
                TransformControls key = { obj.id }
                position = { obj.position } >
                <
                WarehouseObject type = { obj.type }
                /> <
                /TransformControls>
            ))
        } <
        /group> <
        /Selection>

        <
        OrbitControls makeDefault / >
        <
        Environment preset = "city" / >
        <
        /Canvas>

        <
        div style = {
            { position: 'absolute', top: 20, left: 20, zIndex: 100 } } >
        <
        button onClick = {
            () => addObject('rack') }
        style = { toolButtonStyle } >
        Ajouter Rack <
        /button> <
        button onClick = {
            () => addObject('palette') }
        style = { toolButtonStyle } >
        Ajouter Palette <
        /button> <
        /div> <
        /div>
    )
}

const toolButtonStyle = {
    padding: '10px 15px',
    margin: '5px',
    background: '#4361ee',
    color: 'white',
    border: 'none',
    borderRadius: '5px',
    cursor: 'pointer'
}